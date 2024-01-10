import sys, os, shutil
import re
import random
import datetime
import zipfile
import types
sys.path.insert(0, os.path.abspath(""))

import UserModel
import CMSModel as Model
import db_structure

from skeletonz.server import getConfig
from skeletonz.plugins.upload import model as upload_model

from amilib.amiweb import amiweb

TODAY = datetime.date.today()
backup_dir = "dynamic_dirs/backup"
prefix_comment = '-- Skeletonz prefix: '

class DatabaseBackup:

    def __init__(self, rootdir):
        #rootdir should LOOK like backup/temp
        self.rootdir = rootdir

    def dumpTable(self, table_name):
        result = []
        mysqldump = getattr(getConfig(), "MYSQL_DUMP", None) or "mysqldump"
        dumpcmd = "%s -c %s %s" % \
          (mysqldump, self._genMySqlLogin(), table_name)

        data = os.popen(dumpcmd, "rb").read()
        data = re.sub('DEFAULT CHARSET.*?;', "CHARACTER SET utf8;", data)
        return data

    def dumpTables(self):
        tables = Model.getAllTables()
        return "".join([self.dumpTable(t) for t in tables])

    def dumpDatabase(self, filename):
        sql_dump = self.dumpTables()
        sql_dump = "%s%s\n%s" %\
            (prefix_comment, getConfig().TABLE_PREFIX, sql_dump)

        f = open(self._getFile(filename), "w")
        f.write(sql_dump)
        f.close()

        if len(sql_dump) < 500:
            print sql_dump[0:100]
            raise Exception('ERROR: Something in mysqldump went wrong!')

    def restoreDatabase(self, filename):
        #Be sure that it's the same prefix!
        fp = open(self._getFile(filename), "r")
        prefix = fp.readline().replace(prefix_comment, '').strip()

        if prefix != getConfig().TABLE_PREFIX:
            raise Exception('Backup prefix does not match current. Current prefix: "%s". Backup prefix: "%s"' % \
                (getConfig().TABLE_PREFIX, prefix))

        self._dropDBManual()
        self._runSQLFile(filename)

    def _genMySqlLogin(self):
        if getConfig().DB_PASSWORD != "":
            password = "-p%s" % getConfig().DB_PASSWORD
        else:
            password = ""

        return "-h %s -u %s %s %s" %\
            (getConfig().DB_HOST, getConfig().DB_USER, password, getConfig().DB_DATABASE)

    def _getFile(self, filename):
        return "%s/%s" % (self.rootdir, filename)

    def _dropDBManual(self):
        db_structure.dropStructure()

    def _runSQLFile(self, filename):
        cmd = "mysql %s < %s" % \
          (self._genMySqlLogin(), self._getFile(filename))
        os.popen(cmd)



class UploadBackup:

    def copyToDir(self, src, dst):
        for root, dirs, files in os.walk(src):
            for name in files:
                if name != "sk__dump.sql":
                    try:
                        shutil.copyfile(os.path.join(root, name), os.path.join(dst, name))
                    except:
                        print "Could not copy %s" % os.path.join(dst, name)
            #Remove subversion dirs
            if '.svn' in dirs:
                dirs.remove('.svn')

    def rmFiles(self, src):
        #Used to delete the upload map
        for root, dirs, files in os.walk( src ):
            for name in files:
                os.remove(os.path.join(root, name))
            if '.svn' in dirs:
                dirs.remove('.svn')
UploadBackup = UploadBackup()


class ConfigBackup:

    def copyToDir(self, src, dst):
        for root, dirs, files in os.walk(src):
            for name in files:
                dst_name = 'sk_config__%s' % name
                try:
                    shutil.copyfile(os.path.join(root, name), os.path.join(dst, dst_name))
                except:
                    print "Could not copy %s" % os.path.join(dst, name)
            #Remove subversion dirs
            if '.svn' in dirs:
                dirs.remove('.svn')

    def restore(self, src, dst):
        for root, dirs, files in os.walk(src):
            for name in files:
                if name.find('sk_config__') == 0:
                    dst_name = name.replace('sk_config__', '')
                    try:
                        shutil.copyfile(os.path.join(root, name), os.path.join(dst, dst_name))
                    except:
                        print "Could not copy %s" % os.path.join(dst, dst_name)

ConfigBackup = ConfigBackup()


class SnapShot:

    def createZip(self, order, name):
        self._generateRandomTempDir()

        #Move upload files to temp
        UploadBackup.copyToDir(self._getUploadDir(), self._getTempDir() )

        #Dump the database to the temp aswell
        db_backup = DatabaseBackup( self._getTempDir() )
        db_backup.dumpDatabase("sk__dump.sql")

        #Copy the config files
        ConfigBackup.copyToDir(self._getConifgDir(), self._getTempDir())

        zip_fp = zipfile.ZipFile("%s/%s.zip" % (backup_dir, self._generateSnapshotFilename(order, name)), 'w')
        for root, dirs, files in os.walk( self._getTempDir() ):
            for name in files:
                zip_fp.write("%s/%s" % (root, name))
            #Remove subversion dirs
            if '.svn' in dirs:
                dirs.remove('.svn')
        zip_fp.close()

        self._removeTempDir()

    def restoreZip(self, filename):
        #Restore uploads
        self._generateRandomTempDir()
        self._unpackZip(filename, self._getTempDir())

        try:
            DatabaseBackup(self._getTempDir()).restoreDatabase("sk__dump.sql")

            ConfigBackup.restore(self._getTempDir(), self._getConifgDir())
            db_structure.freshLoadConfig()

            UploadBackup.rmFiles(self._getUploadDir())
            UploadBackup.copyToDir(self._getTempDir(), self._getUploadDir())
        finally:
            try:
                self._removeTempDir()
            except:
                print 'Could not delete temp dir, delete "%s" manually' % self._getTempDir()

    def _unpackZip(self, filename, dst_dir):
        zip_fp = zipfile.ZipFile("%s/%s" % (backup_dir, filename), 'r')
        p = re.compile(".*/(.*)")

        for file_full in zip_fp.namelist():
            file_name = p.search(file_full).groups()[0]
            file("%s/%s" % (dst_dir, file_name), 'wb').write( zip_fp.read(file_full) )

        zip_fp.close()

    def _getUploadDir(self):
        return upload_model.UPLOAD_DIR

    def _getConifgDir(self):
        return 'dynamic_config_files'

    def _generateSnapshotFilename(self, order, name):
        t = "%i_%s_%s" % (order, name, TODAY)
        return t

    def _getTempDir(self):
        return "%s/temp%i" % (backup_dir, self.nr)

    def _generateRandomTempDir(self):
        #Generate a new random temp dir
        while True:
            nr = random.randint(1, 20)
            try:
                self._makeTempDir(nr)
                break
            except:
                pass
        self.nr = nr

    def _makeTempDir(self, nr):
        os.mkdir("%s/temp%i/" % (backup_dir, nr))

    def _removeTempDir(self):
        for root, dirs, files in os.walk( self._getTempDir() ):
            for name in files:
                os.remove(os.path.join(root, name))
        os.rmdir(self._getTempDir())


class SnapShots:

    def createSnapObj(self, filename):
        class SnapShotObj: pass

        p = re.compile("(\d+)_(.+)_(.+).zip")
        try: split = p.search(filename).groups()
        except: return

        obj = SnapShotObj()
        obj.filename = filename
        obj.order = split[0]
        obj.name = split[1]
        obj.date = split[2]
        obj.toJSON = '{"order": %s, "content": "%s - %s", "filename": "%s"}' %\
              (obj.order, obj.name, obj.date, obj.filename)
        return obj

    def getAllSnapShots(self):
        #[(order, name, date, filename), ...]
        all = []
        for root, dirs, files in os.walk( "%s/" % backup_dir ):
            for filename in files:
                if filename.find(".zip") != -1:
                    all.append( self.createSnapObj(filename) )
            for dir in dirs:
                dirs.remove(dir)
        all.reverse()
        return all
SnapShots = SnapShots()
