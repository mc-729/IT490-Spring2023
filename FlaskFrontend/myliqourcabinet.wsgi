#!/usr/bin/env python
import os
import sys
import logging
from config import Config
from application import init_app

logging.basicConfig(stream=sys.stderr)
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from application import app as application
application.secret_key = Config.SECRET_KEY

# Initialize Flask application
app = init_app()

# Define WSGI entry point
def application(environ, start_response):
    return app(environ, start_response)