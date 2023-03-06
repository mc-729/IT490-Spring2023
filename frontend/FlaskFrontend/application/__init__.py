from flask import Flask, session
from flask_bootstrap import Bootstrap5
from flask_migrate import Migrate
from flask_wtf import CSRFProtect
from requests import Session
from flask import Flask
from flask_session import Session

from config import Config
import config
from application.bp.authentication import authentication
from application.bp.homepage import bp_homepage
from flask import Flask, render_template, redirect, request, session


migrate = Migrate()
csrf = CSRFProtect()


def init_app():
    """Initialize the core application."""
    app = Flask(__name__, instance_relative_config=False)
    app.config.from_object(config.Config())
    csrf.init_app(app)
    bootstrap = Bootstrap5(app)
    app.config["SESSION_PERMANENT"] = False
    app.config["SESSION_TYPE"] = "filesystem"
    Session(app)

    # Initialize Plugins

    with app.app_context():
        blueprints = [bp_homepage, authentication]
       
        # Register Blueprints
        for blueprint in blueprints:
            app.register_blueprint(blueprint)
        return app
