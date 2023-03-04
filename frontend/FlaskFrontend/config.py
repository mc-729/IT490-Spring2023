import os
import secrets


class Config:
    """Default Config Settings"""
    FLASK_DEBUG = os.getenv('FLASK_DEBUG', "TRUE")
    SECRET_KEY = secrets.token_urlsafe(16)