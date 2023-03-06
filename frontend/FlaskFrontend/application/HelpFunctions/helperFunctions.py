from functools import wraps
from flask import redirect, session, url_for

def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'sessionID' not in session:
            return redirect(url_for('authentication.login'))
        return f(*args, **kwargs)
    return decorated_function
class User:
    def __init__(self, session_id=None, username=None,  email=None, first_name=None, last_name=None):
        self.session_id = session_id
        self.username = username
        self.email = email
        self.first_name = first_name
        self.last_name = last_name
    
    def __str__(self):
        return f"<User {self.username}>"
    
    def __repr__(self):
        return str(self)
    
    def to_dict(self):
        return {
            'session_id': self.session_id,
            'username': self.username,
            'email': self.email,
            'first_name': self.first_name,
            'last_name': self.last_name,
        }
    
    @classmethod
    def from_dict(cls, user_dict):
        return cls(**user_dict)
    def get_session_id(self):
        return self.session_id
    
    def get_username(self):
        return self.username
    
    def get_email(self):
        return self.email
    
    def get_first_name(self):
        return self.first_name
    
    def get_last_name(self):
        return self.last_name
