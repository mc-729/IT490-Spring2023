from flask import Flask, session
from flask_bootstrap import Bootstrap5
from flask_wtf import CSRFProtect
from requests import Session
from flask import Flask
from flask_session import Session
import config
from application.bp.authentication import authentication
from application.bp.homepage import bp_homepage
from application.bp.events import bp_events
from application.bp.apiSearch import  bp_apiSearch
from application.bp.DrinkWithYourEyes import bp_drinkwithyoureyes
from application.bp.jqueryexperiment import bp_jqueryExample
from application.bp.pagination import bp_pagination
from application.bp.myliquorcabinet import bp_liquorcabinet
from flask import Flask, render_template, redirect, request, session


csrf = CSRFProtect()

csrf.exempt('application.bp.events.events')
csrf.exempt('application.bp.DrinkWithYourEyes.data')
csrf.exempt('application.bp.DrinkWithYourEyes.drinkwithyoureyes')
csrf.exempt('application.bp.apiSearch.apiSearch')
csrf.exempt('application.bp.apiSearch.sendDrinkData')
csrf.exempt('application.bp.apiSearch.apiSearchSubmit')
csrf.exempt('application.bp.events.sendEventData')
csrf.exempt('application.bp.myliquorcabinet.liquorcabinet')
csrf.exempt('application.bp.myliquorcabinet.submit_ingredient')
csrf.exempt('application.bp.myliquorcabinet.deleteRecipe')


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

        blueprints = [bp_homepage, authentication, bp_liquorcabinet,bp_apiSearch,bp_drinkwithyoureyes,bp_jqueryExample,bp_pagination,bp_events]

       
        # Register Blueprints
        for blueprint in blueprints:
            app.register_blueprint(blueprint)
        return app
