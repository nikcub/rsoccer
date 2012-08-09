
import json
import re

from app.http import HttpClient

epl_homepage = "http://www.whoscored.com/Regions/252/Tournaments/2/England-Premier-League"
match_feed = "http://www.whoscored.com/Matches/%d"
schedule_feed = "http://www.whoscored.com/tournamentsfeed/6531/Fixtures/?d=%d&isAggregate=false"
schedule_date = 201208

class Whoscored(object):
  
  def __init__(self):
    self.http = HttpClient()
  
  def get_schedule(self):
    schedule_json = self.http.fetch(schedule_feed % schedule_date)
    return self.parse_json(schedule_json)

  def get_table(self):
    html = self.http.fetch(epl_homepage)
    table_json = re.search('\'standings\'\, \{ stageId\: 6531 \}\, (?P<json>[\w\'\[\]\,\s]+)', html).group('json')
    return self.parse_json(table_json)
    
  def clean_json(self, js):
    return js.replace(',,', ',').replace(',,', ',').replace('\'', '"').replace(',]', ']')

  def parse_json(self, js):
    js = self.clean_json(js)
    js_dat = json.loads(js)
    return js_dat
    
