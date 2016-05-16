#The current template1
from templates.Dragon_Template import template
CURRENT_TEMPLATE = template

##
# Server configuration
#

#What port the server runs on
PORT = 8080

#MODE can have following values:
#   "testing": Use this value if you are in development mode.
#   "deployment": Use this value when you are ready to deploy your website.
MODE = "deployment"

#Turn off caching if you are in development mode.
USE_CACHING = True
BUILD_CACHE_ON_START = True


##
# Site configuration
#
TITLE_PREFIX = "#MyWebSite?"
BASE_URL = 'http://mywebsite.com' #Could be BASE_URL = 'http://www.daimi.au.dk/~amix/BiRC'
CURRENT_TEMPLATE.BASE_URL = BASE_URL
COOKIE_NAME = "sk_web_site"

#Admin username and pw
CHECK_LOGIN = True
ADMIN_USERNAME = "user"
ADMIN_PASSWORD = "pass"

#The name of the first created page
START_PAGE = "Main"


##
# Database information
#
TABLE_PREFIX = 'sk_'
DB_USER = 'my_skltns2'
DB_PASSWORD = '1wur_ld!'
DB_HOST = 'here.mysql.com'
DB_DATABASE = 'webcms1' #You will need to create this


##
# Email settings (optional)
#
DEFAULT_EMAIL = 'support@mywebsite.com'

#SMTP_SERVER = 'localhost'
#SMTP_AUTH_REQUIRED = False #Is authorization required?
#SMTP_USER = ''
#SMTP_PASSWORD = ''
