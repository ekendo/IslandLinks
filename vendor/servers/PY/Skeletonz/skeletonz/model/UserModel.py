from skeletonz.server import getConfig
from amilib.amiweb.amiweb import db, session
from amilib import json

class UserDeco:
    """
    Following attributes are added to an object of this class:
      id
      username
      password
      group_id
    """
    def toJSON(self):
        d = {'id': self.id,
            'content': self.username,
            'group_id': self.group_id,
            'user_type': self.user_type}
        return json.write(d)


class Users:

    def checkPassword(self, user, pw_to_check):
        return user.password == pw_to_check

    def getUserByUsername(self, username):
        return db().select("user", "username='%s'" % username, obj_deco=UserDeco, as_one=True)

    def getAllUsers(self):
        return db().select("user", order_by="id", obj_deco=UserDeco)

    def getUserById(self, id):
        return db().select("user", id=id, obj_deco=UserDeco, as_one=True)

    def add(self, username, password, type):
        if username == "admin" or username == "Everyone":
            return None
        else:
            new_user = db().insert("user", username=username, password=password, user_type=type)
            return self.getUserById(new_user)

    def delete(self, id):
        db().delete("user", id=id)

    def checkLogin(self, username, password):
        if not getConfig().CHECK_LOGIN:
            return True

        if username == "" or password == "":
            return False

        if username == getConfig().ADMIN_USERNAME and password == getConfig().ADMIN_PASSWORD:
            return True

        user = self.getUserByUsername(username)
        if user ==  None:
            return False

        #If an extra function is provided then use it to check password
        internal = self.checkPassword(user, password)

        try:
            external = getConfig().PASSWORD_LOOKUP(username, password)
        except AttributeError:
            external = False

        return internal or external

    def changeUserType(self, user_id, type):
        db().update("user", id=user_id, user_type=type)

    def isAdmin(self, username):
        user = self.getUserByUsername(username)
        if not user:
            return False
        return user.user_type == "admin"


class GroupDeco:

    def toJSON(self):
        d = {'id': self.id,
            'content': self.name}
        return json.write(d)

class Groups:

    def getGroupById(self, id):
        return db().select("group", id=id, as_one=True, obj_deco=GroupDeco)

    def getGroupByName(self, name):
        return db().select("group", "name='%s'" % name, as_one=True, obj_deco=GroupDeco)

    def getAllGroups(self):
        return db().select("group", order_by="id", obj_deco=GroupDeco)

    def add(self, name):
        new = db().insert("group", name=name)
        return self.getGroupById(new)

    def delete(self, id):
        db().delete("group", id=id)

    def isInGroup(self, group_name, username):
        user = Users.getUserByUsername(username)
        if user == None:
            return False
        if user.group_id == None:
            return False

        user_group = self.getGroupById(user.group_id)

        if user_group.name == group_name:
            return True
        else:
            return False

    def changeUserGroup(self, group_id, user_id):
        if group_id != "no_group":
            db().update("user", id=user_id, group_id=group_id)
        else:
            db().update("user", id=user_id, group_id=None)

Users = Users()
Groups = Groups()
