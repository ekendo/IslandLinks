# __init__.py
# Copyright (C) 2005, 2006, 2007 Michael Bayer mike_mp@zzzcomputing.com
#
# This module is part of SQLAlchemy and is released under
# the MIT License: http://www.opensource.org/licenses/mit-license.php

from types import *
from amilib.sqlalchemy.sql import *
from amilib.sqlalchemy.schema import *
from amilib.sqlalchemy.orm import *

from amilib.sqlalchemy.engine import create_engine
from amilib.sqlalchemy.schema import default_metadata

def global_connect(*args, **kwargs):
    default_metadata.connect(*args, **kwargs)
    