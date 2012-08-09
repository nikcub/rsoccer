from google.appengine.ext import db

class Greeting(db.Model):
  author = db.StringProperty()
  content = db.StringProperty(multiline=True)
  date = db.DateTimeProperty(auto_now_add=True)