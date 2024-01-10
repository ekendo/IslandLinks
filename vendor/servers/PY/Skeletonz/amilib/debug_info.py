import sys
import platform
import MySQLdb

def getPythonVersion():
    return sys.version_info

def getMySQLversion():
    return MySQLdb.get_client_info()

def getMySQLdbVersion():
    return MySQLdb.version_info

def getOSInfo():
    return platform.version()


def print_info():
    print "\n\nDEBUG INFO TO SEND TO DEVELOPERS OF SKELETONZ:"
    print "   Python version: ", getPythonVersion()
    print "   MySQL version: ", getMySQLversion()
    print "   MySQLdb version: ", getMySQLdbVersion()
    print "   O/S version: ", getOSInfo()
    print ''
