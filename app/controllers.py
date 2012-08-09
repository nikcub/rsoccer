import logging
import webapp2
import reqhandlers

from app.api.whoscored import Whoscored

class Main(reqhandlers.BaseHandler):
  def get(self):
    ws = Whoscored()
    sched = ws.get_schedule()
    table = ws.get_table()
    team = ws.get_team(26)
    for a in sched:
      logging.info(a[0], a[1], a[3])
    for l in table:
      logging.info(l[0], l[1], l[2])

    return self.render('index', {'sched': sched, 'table': table, 'team': team})
