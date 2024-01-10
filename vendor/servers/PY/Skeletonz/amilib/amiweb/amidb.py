#!/usr/bin/python
# -*- coding: utf-8 -*-

__author__ = 'amix'
__version__ = '2.0 Alpha'
__doc__ = """
$Id: amidb.py 19 2006-12-09 11:33:12Z amix $

Example of usage:
  >>> dbinfo = amidb.AmiDBInfo(user='myuser', password='mypwd', db='testdb', dbtype=amidb.T_MYSQL)
  >>> pool = amidb.ConnectionPool(dbinfo)
  >>> con = pool.getConnection()
  >>> ob = con.select('author', id=5)
  >>> print ob.name
  John Doe
  >>> ob.name = 'Jonny' # you can also write ob.setFieldValue('name', 'Jonny')
  >>>
  >>> print ob.name
  Jonny
  >>> # adding new field (must be defined in db schema already)
  >>> # makes sense if migrating from one db to another
  >>> ob._addField('street', 'Morningstreet')
  >>> ob.save('author', con) # write back changed dataset

Contributions:
  - PostgreSQL support (supports pgsql driver)
  - Extended object decoration and writeback support
  - Read-Only support for connections
  - AmiDBInfo for local configuration
  - ZPsycopgDA support for use of amidb within Zope
  - Bugfix for select method not using id when **kw is filled
  [Simon Pamies (s.pamies@banality.de)]
"""

# Some type shortcuts
#   1: MySQL
#   2: PostgreSQL
#   3: ZPsycopgDA (Zope adapter that supports isolated transactions)
#   4: Sqlite (standalone file-based DB included in python2.5)
T_MYSQL = 1
T_PGDB = 2
T_SQLITE = 3

# Settings for default database
DEFAULT_DATABASE = T_MYSQL

# Set charset
# utf-8 or iso-8859-1
DEFAULT_CHARSET = 'utf-8'
FORCE_UNICODE = False

# Set to true if you always want
# to strip contents of string fields
# when using save on objects
ALWAYS_USE_STRING_STRIPPING=True

# DB stuff
HAS_MYSQL = False
HAS_PGDB = False
HAS_SQLITE = False

#Replicator XXX: Should be more generic
REPLICATOR = None

if DEFAULT_DATABASE == T_MYSQL:
    import cursors

import thread

try:
    import MySQLdb
    HAS_MYSQL = True
except ImportError:
    pass

try:
    import pgdb
    HAS_PGDB = True
except ImportError:
    pass


# up to Python2.4, sqlite is shipped as an external library (as pysqlite2)
try:
    from pysqlite2 import dbapi2 as sqlite
    HAS_SQLITE = True
except ImportError:
    pass

# since Python2.5, it is "batteries included" (as sqlite3)
try:
    import sqlite3 as sqlite
    HAS_SQLITE = True
except ImportError:
    pass

TIMEOUT = 10800 #Interactive timeout. ~ 3 hours

import sqlalchemy.pool as pool
import types

# we need a dict wrapper for some db
def dict_factory(caller, row):
    d = {}
    for idx, col in enumerate(caller.description):
        d[col[0]] = row[idx]
    return d

HAS_JSON = True
try:
    from amilib import json
except ImportError:
    HAS_JSON = False


class AmiDBInfo:
    """ Class that stores database information. Makes it possible
    to hold multiple connections using this module. """

    def __init__(self, user, password='', db='', host='localhost', prefix='', charset=DEFAULT_CHARSET, readonly=False, dbtype=DEFAULT_DATABASE, port=3306, unix_socket=None):
        self.user = user
        self.password = password
        self.db = db
        self.host = host
        self.prefix = prefix
        self.charset = charset
        self.readonly = readonly
        self.port = port
        self.unix_socket = unix_socket

        # creating a DSN for use with some DB adapters
        self.dsn = "dbname='%s' host='%s' user='%s' password='%s'" % (db, host, user, password)

        # AmiDBInfo handles database type
        self.dbtype = dbtype


class AmiDBWrapper:

    def __init__(self, con, dbinfo=None, pool=None):
        self.con = con
        self.pool = pool
        self.dbinfo = dbinfo
        self.is_closed = False

    def getPrefix(self):
        return self.dbinfo.prefix

    def select(self, table, where="", id=None, cols="*", order_by="", group_by="", reversed=False, with_prefix=True, as_one=False, obj_deco=None, decorate=None, limit=None, parameter_mode='AND', having="", **kw):
        """
         Used to select results from a table.

         @param table - A string or a list of tables that we should do a select from
         @param cols - A string or a list of columns we select
         @param where - A string or a list of SQL statements
         @param order_by - A string or a list of SQL statements to order the select by
        """
        #print "[amidb]select:before where<br>"
        
        where = self._appendToWhere(where, kw)

        if with_prefix:
            table = self._joinIfList(table, prefix=self.dbinfo.prefix)
        else:
            table = self._joinIfList(table)

        cols = self._joinIfList(cols)
        if not id in [None, '']:
            where.append("id=%s" % id)

        where = self._joinIfList(where, " %s " % parameter_mode)

        sql = []
        sql.append("SELECT %s FROM %s" % (cols, table))


        if where not in [None, "", []]:
            sql.append("WHERE %s" % where)

	#print "[amidb]select:after where & before group by<br>"

        group_by = self._joinIfList(group_by)
        if group_by not in [None, "", []]:
            sql_group_by = 'GROUP BY %s' % group_by
            sql.append(sql_group_by)

        having = self._joinIfList(having)
        if having not in [None, "", []]:
            sql_having = 'HAVING %s' % having
            sql.append(sql_having)

        order_by = self._joinIfList(order_by)
        if order_by not in [None, "", []]:
            sql_order_by = reversed and "ORDER BY %s DESC" % order_by or "ORDER BY %s" % order_by
            sql.append(sql_order_by)

	#print "[amidb]select:after where & before limit<br>"

        if limit != None:
            if type(limit) == types.TupleType:
                sql.append("LIMIT %s, %s" % (limit[0], limit[1]))
            else:
                sql.append("LIMIT %s" % limit)

        result = []
        for r in self.query(" ".join(sql)):
            o = AttrDict(r)
            if obj_deco == None:
                result.append(o)
            else:
                obj = obj_deco()

                if hasattr(obj_deco, 'update'):
                    obj.update(o)
                else:
                    obj.__dict__.update(o)

                #Try to see if the obj has toJSON, if not, add the default
                try:
                    to_json = obj.toJSON
                except AttributeError:
                    setattr(obj, "toJSON", o.toJSON)

                if hasattr(obj, 'init'):
                    obj.init()

                result.append(obj)
	
	#print "[amidb]select:after where & before as_one<br>"


        if as_one == True:
            #print "[amidb]select: as_one == True<br>"
            if len(result) == 0:
                #print "[amidb]select: len(result) == 0<br>"    
                return None
            else:
                #print "[amidb]select: before result[0]<br>"  
                return result[0]
                
        #print "[amidb]select:after where & before final result<br>"
           
        return result

    def selectMax(self, table, column, **kw):
        res = self.select(table, cols="MAX(%s)" % column, as_one=True, **kw)
        if res:
            return res.values()[0]
        return None

    def selectMin(self, table, column, **kw):
        res = self.select(table, cols="MIN(%s)" % column, as_one=True, **kw)
        if res:
            return res.values()[0]
        return None

    def selectCount(self, table, column='*', distinct=False, **kw):
        if distinct:
            column = 'DISTINCT %s' % column
        res = self.select(table, cols="COUNT(%s)" % column, as_one=True, **kw)
        if res:
            return res.values()[0]
        return None

    def _replaceInsert(self, op, table, with_prefix, **kw):
        if kw:
            if with_prefix:
                table = self._appendPrefix(table)
            sql = []
            sql.append("%s INTO %s" % (op, table))
            sql.append("(%s)" % (", ".join(kw.keys())))

            values = map(self._toValue, kw.values())
            sql.append("VALUES (%s)" % ", ".join(values))

            return self.query(" ".join(sql))
        else:
            return None

    def insert(self, table, with_prefix=True, **kw):
        """
        Used to insert a row in a table.
        """
        return self._replaceInsert('INSERT', table, with_prefix, **kw)

    def replace(self, table, with_prefix=True, **kw):
        """
        Used to replace a row in a table.
        """
        return self._replaceInsert('REPLACE', table, with_prefix, **kw)

    def update(self, table, where="", id=None, with_prefix=True, **kw):
        """
        Used to update a row in a table

        @param where - A string or a list of SQL statements
        """
        if id != None:
            id = "id=%s" % id
            if type(where) == types.ListType:
                where.append(id)
            else:
                where = [id]

        where = self._joinIfList(where, " AND ")

        if kw:
            if with_prefix:
                table = self._appendPrefix(table)
            sql = []
            sql.append("UPDATE %s SET" % table)
            sql.append(", ".join(["%s=%s" % (k, self._toValue(v, k)) for k, v in kw.items()]))
            if where:
                sql.append("WHERE %s" % where)
            return self.query(" ".join(sql))

    def delete(self, table, where="", id=None, with_prefix=True, **kw):
        """
        Used to delete rows from a table.

        @param where - A string or a list of SQL statements
        """
        where = self._appendToWhere(where, kw)
        if with_prefix:
            table = self._appendPrefix(table)
        if id == None:
            where = self._joinIfList(where, " AND ")
        else:
            where = "id=%s" % id
        sql = "DELETE FROM %s WHERE %s" % (table, where)
        return self.query(sql)

    def _query(self, sql, commit=False, expect_result=True):
        IS_INSERT = False
        if sql.find('INSERT')>=0 or sql.find('UPDATE')>=0:
            IS_INSERT = True

        if self.dbinfo.dbtype == T_PGDB:
            cur = self.con.cursor()
            cur.row_factory = lambda cur, row: dict_factory(cur, row)
        elif self.dbinfo.dbtype == T_MYSQL:
            cur = self.con.cursor(cursors.DictCursor)
        elif self.dbinfo.dbtype == T_SQLITE:
            self.con.connection.row_factory = dict_factory
            cur = self.con.cursor()
            # clean the query from non allowed keywords
            sql = sql.replace('auto_increment', 'not null primary key autoincrement')
            sql = sql.replace('PRIMARY KEY (id),\n','')

        # Run the query
        if type(sql) == types.StringType:
            sql = unicode(sql, self.dbinfo.charset)
            sql = sql.encode(self.dbinfo.charset)

        try:
            if self.dbinfo.dbtype == T_MYSQL:
                try:
                    rc = cur.execute(sql)
                    self.last_query = sql
                except MySQLdb.IntegrityError, e:
                    print e
                    raise IntegrityError
                except:
                    print sql
                    raise

            # Postgres support
            elif self.dbinfo.dbtype == T_PGDB:
                try:
                    rc = cur.execute(sql)
                except:
                    raise

            # Sqlite support
            if self.dbinfo.dbtype == T_SQLITE:
                try:
                    cur.execute(sql)
                except:
                    raise

            # Execute the transaction
            if expect_result == True:
                if not IS_INSERT:
                    result = cur.fetchall()
                last_id = cur.lastrowid
        finally:
            cur.close()

        if expect_result != True:
            return None

        if IS_INSERT:
            return last_id
        else:
            return result

    def query(self, sql, attr_style=False, **kw):
        q = self._query(sql, **kw)
        if attr_style == True:
            return map(AttrDict, q)
        else:
            return q

    def commmit(self):
        self.con.commit()

    def _toValue(self, v, k=None):
        if type(v) in [types.IntType, types.LongType, types.FloatType]:
            return "%s" % str(v)
        if v == True:
            if self.dbinfo.dbtype == T_MYSQL:
                return "1"
            else: return "TRUE"

        if v == False:
            if self.dbinfo.dbtype == T_MYSQL:
                return "0"
            else: return "FALSE"

        if v == None:
            return "null"
        else:
            if type(v) == types.StringType:
                v = unicode(v, self.dbinfo.charset)

            if type(v) == types.UnicodeType:
                v = v.encode(self.dbinfo.charset)
            else:
                v = str(v)
            return "'%s'" % self.escape(v)

    def _reprSqlPair(self, k, v):
        if v == None:
            return "%s is null" % k
        else:
            return "%s=%s" % (k, self._toValue(v))

    def escape(self, str):
        if self.dbinfo.dbtype == T_MYSQL:
            return MySQLdb.escape_string(str)
        elif self.dbinfo.dbtype == T_PGDB:
            return pgdb.escape_string(str)

        return str

    def _appendToWhere(self, where, kw):
        """
        This is used to append unknown keywoard as extra items to the where clause
        """
        k_val = [self._reprSqlPair(k, v) for k, v in kw.items()]
        if where != "" and type(where) in [types.StringType, types.UnicodeType]:
            k_val.append(where)
        elif where != "":
            k_val.extend(where)
        return k_val

    def _appendPrefix(self, t):
        return "%s%s" % (self.dbinfo.prefix, t)

    def _joinIfList(self, l, sep=", ", prefix=""):
        if prefix != "":
            if type(l) == types.ListType:
                l = map(lambda e: "%s%s" % (prefix, e), l)
                return sep.join(l)
            else:
                return "%s%s" % (prefix, l)
        else:
            if type(l) == types.ListType:
                return sep.join(l)
            else:
                return l

    def close(self):
        self.con.close()


class ConnectionPool:

    def __init__(self, dbinfo, pool_size=8):
        self.dbinfo = dbinfo

    def createConnection(self):
        #print "[amidb]ConnectionPool/createConnection:<br>"
        con = None
        di = self.dbinfo

        if di.unix_socket:
            con = MySQLdb.connect(unix_socket=di.unix_socket,
                    user=di.user,
                    passwd=di.password,
                    db=di.db)
        else:
            #con = MySQLdb.connect(port=di.port,db=di.db,user=di.user,passwd=di.password,host=di.host)
            #print "[amidb]createConnection: host"
            #print di.host
            #print "<br>"
            #print "[amidb]createConnection: user"
	    #print di.user
            #print "<br>"
            #print "[amidb]createConnection: password"
	    #print di.password
            #print "<br>"
            #print "[amidb]createConnection: db"
	    #print di.db
            #print "<br>"
            con = MySQLdb.connect(host = di.host,user = di.user,passwd = di.password,db = di.db)
	    #con = MySQLdb.connect(host = "ekendo.hypermartmysql.com",user = "ekendo_skltns2",passwd = "1glo_bal!",db = "ekendocms1")

        cur = con.cursor()
        cur.execute("SET autocommit = 1")
        cur.close()

        if con:
            return con

        raise Exception, 'Could not connect! dbtype is %s' % dbinfo.dbtype

    def getConnection(self):
        di = self.dbinfo
        con = self.createConnection()
        #print "[amidb]ConnectionPool/getConnection:after con set<br>"
        wrap = AmiDBWrapper(con, self.dbinfo, pool=self)
        return wrap

    def releaseConnection(self, wrapper):
        wrapper.con.close()

    def status(self):
        print self.pool.status()

class AttrDict(dict):

    __allow_access_to_unprotected_subobject__ = 1

    def __init__(self, *args, **kwargs):
        dict.__init__(self, *args, **kwargs)

    def __getattr__(self, name):
        if name != "toJSON":
            try:
                return self[name]

            # we want to behave like an object
            except KeyError: raise AttributeError

    def toJSON(self):
        if not HAS_JSON:
            return ''

        if self.__dict__.has_key("JSON"):
            return self.JSON
        else:
            return json.write(self)


class IntegrityError(Exception):
    pass


class Replicator:
    """
    Replicates write queries to another database
    """
    def __init__(self, dbinfo):
        self.dbinfo = dbinfo
        self.pool = ConnectionPool(dbinfo)

    def execute(self, query):
        wrap = self.pool.getConnection()
        if query.find('SELECT') == -1:
            cur = wrap.con.cursor(cursors.DictCursor)
            try:
                cur.execute(query)
            finally:
                cur.close()

    def executeInsert(self, query, id):
        wrap = self.pool.getConnection()
        cur = wrap.con.cursor(cursors.DictCursor)

        try:
            first_p = query.find(')')
            last_p = query.rfind(')')
            new_query = '%s, id)%s, %i)' % (query[0:first_p], query[first_p+1:last_p], id)
            cur.execute(new_query)
        finally:
            cur.close()
