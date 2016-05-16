#! /usr/bin/python
import os, sys, MySQLdb
file_dir = os.path.abspath(os.path.dirname(__file__))
os.chdir(file_dir)
sys.path.insert(0, os.path.abspath("amilib"))

import general_config
from skeletonz import server
server.general_config = general_config

#server.startServer()
