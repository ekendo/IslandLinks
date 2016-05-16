import os, sys
sys.path.insert(0, os.path.abspath("amilib"))

from amilib.amiweb import amidb, amiweb

import general_config
import server

db_pool = None
def getPool():
    global db_pool
    if not db_pool:
        d = {
            "host": general_config.DB_HOST,
            "user": general_config.DB_USER,
            "password": general_config.DB_PASSWORD,
            'db': general_config.DB_DATABASE,
            'prefix': general_config.TABLE_PREFIX
        }
        dbinfo = amidb.AmiDBInfo(**d)
        db_pool = amidb.ConnectionPool(dbinfo)
    return db_pool

def getConnection():
    return getPool().getConnection()

def releaseConnection(con):
    getPool().releaseConnection(con)

def setUpAmiWeb():
    server.general_config = general_config
    server.setUpDatabase()
    amiweb.setUpConnection()
