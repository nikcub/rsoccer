#!/usr/bin/env python
# -*- coding: utf-8 -*-
# vim:ts=4:sw=4:expandtab
"""
  http client.
"""
import logging
from google.appengine.api import urlfetch

class HttpClient(object):
  """docstring for HttpClient"""

  _req_headers = {
    "User-Agent": "Sketch v0.0.1 <+http://nikcub.appspot.com/projects/sketch>"
  }
  _req_attempts = 3

  def __init__(self, cache_options=None):
    """docstring for __init__"""

  def cache_handler(self):
    return self._cache_handle

  def conditional(self):
    self._req_headers['If-Modified-Since'] = "Sat, 29 Oct 1994 19:43:31 GMT"

  def __get__(self, **kwargs):
    logging.debug("Called __call__ with:")
    logging.debug(**kwargs)

  def fetch(self, url):
    attempt = 1
    result = None
    self._req_headers['Connection'] = 'Close'

    while attempt <= self._req_attempts:
      try:
        result = urlfetch.fetch(
          url,
          method = urlfetch.GET,
          headers = self._req_headers,
          deadline = 20
        )
      except urlfetch.DownloadError, e:
        logging.info("httpclient: (Download Attempt [%d/%d]) DownloadError: Download timed out"
          % (attempt, self._req_attempts))
        attempt += 1
      except Exception, e:
        logging.exception("httpclient: Exception: %s" % e.message)
        logging.exception("httpclient: Exceeded number of attempts allowed")
        return False

      if result:
        logging.info(result)
        if result.status_code == 200:
          return result.content.decode('UTF-8')
        if result.status_code == 304:
          # not modified - get from cache
          pass

    return False
