#!/usr/bin/python
#print "Content-type:text/html\n\n"
#print "Content-type:application/json\n\n"
print "Content-type:application/javascript\n\n"
import MySQLdb
import json
import os,sys


#Variables
# Main Rest URL : [mysite].com/Data/py/bkdtakbdb.py/[ServerName]/English/Home
user_agent = os.environ["HTTP_USER_AGENT"]
ip = os.environ["REMOTE_ADDR"]
url = os.environ["SCRIPT_URI"]
langId = 0
page = 'NoWhere'
pageId =0
pageSection = 'NoWhere'
sqlString = ''

if(url.find('English')>0):
  langId=1

if(url.find('Home')>0):
  pageId = 1
  page='home'

if(url.find('Ads')>0):
  pageId = 2
  page = 'ads'
  if(url.find('Examples')>0):
     pageSection = 'Answers'

if(url.find('Experiments')>0):
  pageId = 3
  page= 'experiments'

if(url.find('Questions')>0):
  pageId = 4
  page= 'questions'

if(url.find('Answers')>0):
  pageId = 5
  page= 'answers'

if(url.find('Products')>0):
  pageId = 6
  page= 'products'
  if(url.find('Examples')>0):
     pageSection = 'Answers'

if(url.find('Solutions')>0):
  pageId = 7
  page= 'solutions'

if(url.find('Problems')>0):
  pageId = 8
  page= 'problems'

if(url.find('Definitions')>0):
  pageId = 9
  page= 'definitions'

if(url.find('Charts')>0):
  pageId = 10
  page= 'charts'

if(url.find('Maps')>0):
  pageId = 11
  page= 'maps'

if(url.find('Profiles')>0):
  pageId = 12
  page= 'profiles'

if(url.find('Registrations')>0):
  pageId = 13
  page= 'registrations'

if(pageSection=='NoWhere'):
    sqlString = 'SELECT Comments.Comment,Subjects.Language, Subjects.tag1, Subjects.tag2, Subjects.tag3, Definitions.Definition, Definitions.tag1 as tag4, Definitions.tag2 as tag5, Definitions.tag3 as tag6, Questions.Question, Questions.tag1 as tag7, Questions.tag2 as tag8, Questions.tag3 as tag9, Comments.tag1 as tag10, Comments.tag2 as tag11, Comments.tag3 as tag12 FROM Subjects, Questions, Definitions, Comments WHERE (Subject = Definitions.tag1 OR Subject = Definitions.tag2 OR Subject = Definitions.tag3) AND (Definitions.tag1=Questions.tag1 OR Definitions.tag1=Questions.tag2 OR Definitions.tag1=Questions.tag3) and Definitions.language_id = '+str(langId)+' and Definitions.definition_id= '+str(pageId) + ' and Subjects.Language = Questions.Language and Comments.definition_id = Definitions.definition_id and Comments.subject_id = Subjects.subject_id'

if((pageSection=='Answers')and(pageId==2)):
    sqlString = 'SELECT Answers.Answer, Answers.tag1, Answers.tag2, Answers.tag3, Answer.tag1 as tag4, Answer.tag2 as tag5, Answer.tag3 as tag6, Examples.Example, Examples.tag1 as tag7, Examples.tag2 as tag8, Examples.tag3 as tag9, Elements.Element, Elements.element_type FROM Answers, Answer, Examples, Elements WHERE 	Answers.tag1 = "#Ads" and (Answers.tag1=Answer.tag1 OR Answers.tag1=Answer.tag2 OR Answers.tag1=Answer.tag3) and Answer.example_id = Examples.example_id and Examples.element_id = Elements.element_id'

if((pageSection=='Answers')and(pageId==6)):
    sqlString = 'SELECT Answers.Answer, Answers.tag1, Answers.tag2, Answers.tag3, Answer.tag1 as tag4, Answer.tag2 as tag5, Answer.tag3 as tag6, Examples.Example, Examples.tag1 as tag7, Examples.tag2 as tag8, Examples.tag3 as tag9, Elements.Element, Elements.element_type FROM Answers, Answer, Examples, Elements WHERE 	Answers.tag1 = "#Products" and (Answers.tag1=Answer.tag1 OR Answers.tag1=Answer.tag2 OR Answers.tag1=Answer.tag3) and Answer.example_id = Examples.example_id and Examples.element_id = Elements.element_id'

try:
 conn = MySQLdb.connect (
  host = "thisis.mysite.com",
  user = "_mysiteData3",
  passwd = "od_pass",
  db = "my_data")

 '''
 for param in os.environ.keys():
     print "<br><b>%20s</b>: %s<\br>" % (param, os.environ[param])
 '''
 #print 'url:'+url
 #print sqlString 
 cur = conn.cursor(MySQLdb.cursors.DictCursor)
 cur.execute(sqlString)
 #print "<br>["
 rows = cur.fetchall()
 for row in rows:
   print "{"
   print '"'+page+'":{' 
   if(pageSection=='NoWhere'):
       print '"question":"' +str(row["Question"]).replace('"',"'")+'",'
       print '"definition":"' +str(row["Definition"]).replace('"',"'")+'",'
       print '"comment":"' +str(row["Comment"]).replace('"',"'")+'",'
   if((pageSection=='Answers')and(pageId==2)):
       print '"answer":"' +str(row["Answer"]).replace('"',"'")+'",'
       print '"example":"' +str(row["Example"]).replace('"',"'")+'",'
       print '"element":"' +str(row["Element"]).replace('"',"'")+'",'
   if((pageSection=='Answers')and(pageId==6)):
       print '"answer":"' +str(row["Answer"]).replace('"',"'")+'",'
       print '"example":"' +str(row["Example"]).replace('"',"'")+'",'
       print '"element":"' +str(row["Element"]).replace('"',"'")+'",'
   print '"tag1":"'+str(row["tag1"])+'",'
   print '"tag2":"'+str(row["tag2"])+'",'
   print '"tag3":"'+str(row["tag3"])+'",'
   print '"tag4":"'+str(row["tag4"])+'",'
   print '"tag5":"'+str(row["tag5"])+'",'
   print '"tag6":"'+str(row["tag6"])+'",'
   print '"tag7":"'+str(row["tag7"])+'",'
   print '"tag8":"'+str(row["tag8"])+'",'
   print '"tag9":"'+str(row["tag9"])+'"'
   print "}"
   print "}"

 if pageId==0:
   print '{"firstName":"John", "lastName":"Doe"}'
except MySQLdb.Error, e:
 print "Error %d: %s" % (e.args[0], e.args[1])
 sys.exit (1)
finally:        
    if con:    
        con.close()

