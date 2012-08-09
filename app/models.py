from google.appengine.ext import db

class Greeting(db.Model):
  author = db.StringProperty()
  content = db.StringProperty(multiline=True)
  date = db.DateTimeProperty(auto_now_add=True)

class Match(db.Model):
	league = db.IntegerProperty()
	roundno = db.IntegerProperty()
	home_team = db.ReferenceProperty(Team)
	away_team = db.ReferenceProperty(Team)

class Team(db.Model):
	league = db.IntegerProperty()
	name_short = db.StringProperty()
	name_long = db.StringProperty()
	ground_name = db.StringProperty()

class Player(db.Model):
	club = db.ReferenceProperty(Team)
	first_name = db.StringProperty()
	last_name = db.StringProperty()
	position = db.StringProperty()
	country = db.StringProperty()
	age = db.IntegerProperty()


