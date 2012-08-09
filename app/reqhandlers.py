
import webapp2
import jinja2
import os

  
class BaseHandler(webapp2.RequestHandler):
  
  def render(self, template_name, template_vars):
    template_path = self.get_template_path()
    self.jinja_env = jinja2.Environment(loader=jinja2.FileSystemLoader(template_path))
    template = self.jinja_env.get_template(template_name + '.html')
    content = template.render(template_vars)
    self.render_content(content)

  def render_content(self, content, response_code=200, headers = []):
    self.response.clear()
    if len(headers) > 0:
      for hn, hv in headers:
        self.response.headers[hn] = hv
    self.response.set_status(response_code)
    self.response.out.write(content)
    
  def get_template_path(self):
    curdir = os.path.dirname(__file__)
    template_dir = os.path.join(curdir, '..', 'templates')
    return template_dir
    # sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'vendor'))
  