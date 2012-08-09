#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
# Copyright (c) 2012, Nik Cubrilovic. All rights reserved.
#
# <nikcub@gmail.com> <http://nikcub.appspot.com>  
#
# Licensed under a BSD license. You may obtain a copy of the License at
#
#     http://nikcub.appspot.com/bsd-license
#
""" default GAE app
"""
import sys
import os
import webapp2

sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'vendor'))
from app.routes import routes

debug = True
app = webapp2.WSGIApplication(routes, debug)
