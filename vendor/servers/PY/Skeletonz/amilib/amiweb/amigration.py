from amiweb import db
import types

Not_Created = -1

##
# Keys
#
def PrimaryKey(*k):
    return "PRIMARY KEY (%s)" % (", ".join(k))

def UniqueKey(name, *k):
    return "UNIQUE KEY %s (%s)" % (name, ", ".join(k))

def IndexKey(name, *k):
    return "KEY %s (%s)" % (name, ", ".join(k))

def FulltextKey(name, *k):
    return "FULLTEXT %s (%s)" % (name, ", ".join(k))

##
# Column types
#
class Column:
    def reprOptions(self):
        repr = []
        for k, w in self.options.items():
            if k == "auto_increment":
                repr.append(k)
            if k == "not_null":
                repr.append("NOT NULL")
            if k == "default":
                d = self.options[k]
                if d == None:
                    d = "NULL"
                else:
                    if type(d) in [types.StringType, types.UnicodeType]:
                        d = "'%s'" % d.replace("'", "\\'")
                    else:
                        d = str(d)
                repr.append("DEFAULT %s" % d)
        return "%s" % (" ".join(repr))

class IntCol(Column):
    def __init__(self, name, length=None, **kw):
        self.name = name
        self.length = length
        self.options = kw

    def __repr__(self):
        l = ""
        if self.length:
            l = "(%i)" % self.length
        sql = "%s INT%s" % (self.name, l)
        return " ".join([sql, self.reprOptions()])

class StringCol(Column):
    def __init__(self, name, length=None, **kw):
        self.name = name
        self.length = length
        self.options = kw

    def __repr__(self):
        if self.length:
            sql = "%s VARCHAR(%s)" % (self.name, self.length)
        else:
            sql = "%s TEXT" % (self.name)
        return " ".join([sql, self.reprOptions()])

class DateTimeCol(Column):
    def __init__(self, name, **kw):
        self.name = name
        self.options = kw

    def __repr__(self):
        sql = "%s DATETIME" % self.name
        return " ".join([sql, self.reprOptions()])

class DateCol(Column):
    def __init__(self, name, **kw):
        self.name = name
        self.options = kw

    def __repr__(self):
        sql = "%s DATE" % self.name
        return " ".join([sql, self.reprOptions()])

##
# Main class
#
sql_create_table = """CREATE TABLE %(extra_options)s%(name)s (
  %(items)s
)"""

class AmiGration:

    def __init__(self):
        self.table_prefix = ""

    def _concatSQL(self, k, with_br=False):
        elms = map(lambda e: str(e).rstrip(), list(k))
        if with_br:
            return ",\n  ".join(elms)
        else:
            return ", ".join(elms)

    def _appendPrefix(self, s):
        return "%s%s" % (self.table_prefix, s)

    ##
    # Create and drop table
    #
    def createTable(self, name, *k, **kw):
        name = self._appendPrefix(name)
        extra_options = []
        if kw.get("ignore_if_created", False):
            extra_options.append("IF NOT EXISTS")

        items = self._concatSQL(k, True)

        sql = [sql_create_table %\
            {'name': name, 'items': items, 'extra_options': "%s " % (" ".join(extra_options))}]

        sql.append(' ENGINE=%s' % kw.get('engine', 'MyISAM'))

        if kw.get('utf8', False):
            sql.append(' CHARACTER SET utf8')

        result = "%s;" % (" ".join(map(str, sql)))
        db().query(result)

    def dropTable(self, *k):
        db().query("DROP TABLE IF EXISTS %s;" % ", ".join(map(self._appendPrefix, k)))

    def renameTable(self, old, new):
        old = self._appendPrefix(old)
        new = self._appendPrefix(new)
        db().query("RENAME TABLE %s TO %s" % (old, new))


    ##
    # Add and remove columns
    #
    def addColumn(self, name, *k):
        name = self._appendPrefix(name)
        items = self._concatSQL(k)
        db().query("ALTER TABLE %s ADD (%s);" % (name, items))

    def removeColumn(self, name, *k):
        name = self._appendPrefix(name)
        sql = ["ALTER TABLE %s DROP COLUMN %s;" % (name, key) for key in k]
        db().query("\n".join(sql))

    def changeColumn(self, name, k):
        name = self._appendPrefix(name)
        sql = "ALTER TABLE %s MODIFY %s;" %\
            (name, str(k))
        db().query(sql)

    def renameColumn(self, name, n_from, k):
        name = self._appendPrefix(name)
        sql = "ALTER TABLE %s CHANGE %s %s;" %\
            (name, n_from, str(k))
        db().query(sql)

    def removeKey(self, name, *k):
        sql = ["ALTER TABLE %s DROP KEY %s;" % (self._appendPrefix(name), key) for key in k]
        db().query("\n".join(sql))

    ##
    # Add or remove indexes
    #
    def addIndex(self, name, index_name, *k):
        keys = ','.join(k)
        sql = "ALTER TABLE %s ADD INDEX %s (%s);" % (self._appendPrefix(name), index_name, keys)
        db().query(sql)

    def removeIndex(self, name, index_name):
        db().query("ALTER TABLE %s DROP INDEX %s;" % (self._appendPrefix(name), index_name))



err_str = "Revision '%s' not found in revision list!"

class AMigrationControl:

    def __init__(self, migration_name, revision_list, configurator):
        self.revision_list = revision_list
        self.mi_name = migration_name
        self.current_rev = Not_Created
        self.configurator = configurator
        self.loadRevisionFromFile()

    def _findIndex(self, rev_class_name, revision_list):
        if type(rev_class_name) == types.IntType:
            return rev_class_name
        i = 0
        for o in revision_list:
            if o.__name__ == rev_class_name:
                return i
            i += 1
        return None

    def invalidate(self):
        self.configurator.invalidate()
        self.loadRevisionFromFile()

    def loadRevisionFromFile(self):
        loaed_rev = self.configurator.get('Model_%s' % self.mi_name, "Current revision")
        if loaed_rev == None:
            loaed_rev = Not_Created

        self.current_rev = loaed_rev

    def saveRevisionToFile(self):
        c_rev = self.current_rev
        if c_rev == Not_Created:
            c_rev = "Not_Created"
        revisions = '[%s]' % ", ".join([e.__name__ for e in self.revision_list])
        self.configurator.set('Model_%s' % self.mi_name, "All revisions", revisions)
        self.configurator.set('Model_%s' % self.mi_name, "Current revision", c_rev)

    def upgradeToLatest(self):
        rev_index = self._findIndex(self.current_rev, self.revision_list)

        if rev_index == None and self.current_rev != 'Not_Created':
            raise Exception(err_str % self.current_rev)

        #We are at the latest
        if rev_index == len(self.revision_list)-1:
            return

        if self.current_rev == 'Not_Created':
            rev_index = -1

        revs_to_upgrade = []
        for i in range(rev_index+1, len(self.revision_list)):
            revs_to_upgrade.append(self.revision_list[i])
        try:
            for rev in revs_to_upgrade:
                rev().up()
            self.current_rev = revs_to_upgrade[-1].__name__
        finally:
            self.saveRevisionToFile()

    def downgradeTo(self, dest_rev):
        """
        dest_rev: Can be an index in revision_list or a class name
        """
        c_rev_index = self._findIndex(self.current_rev, self.revision_list)
        d_rev_index = self._findIndex(dest_rev, self.revision_list)

        if c_rev_index == None and self.current_rev != 'Not_Created':
            raise Exception(err_str % self.current_rev)
        if d_rev_index == None and dest_rev != 'Not_Created':
            raise Exception(err_str % dest_rev)

        if c_rev_index <= d_rev_index or self.current_rev == 'Not_Created':
            self.saveRevisionToFile()
            return

        #If we want the dest_rev to be Not_Created
        if dest_rev == Not_Created:
            if c_rev_index == 0:
                revs_to_dgrade = [self.revision_list[0]]
            else:
                revs_to_dgrade = self.revision_list[0::c_rev_index]
        else:
            revs_to_dgrade = self.revision_list[d_rev_index+1::c_rev_index]

        revs_to_dgrade.reverse()
        for rev in revs_to_dgrade:
            try:
                rev().down()
            except AttributeError:
                pass

        self.current_rev = dest_rev
        self.saveRevisionToFile()
