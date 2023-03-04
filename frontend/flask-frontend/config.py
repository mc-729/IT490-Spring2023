import os


class Config:
    """Default Config Settings"""
    FLASK_DEBUG = os.getenv('FLASK_DEBUG', "TRUE")