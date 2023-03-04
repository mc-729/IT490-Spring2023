from flask import Flask
from flask_bootstrap import Bootstrap5
from flask_migrate import Migrate
from flask_wtf import CSRFProtect

import config
from application.bp.authentication import authentication
from application.bp.homepage import bp_homepage

migrate = Migrate()
csrf = CSRFProtect()


def init_app():
    """Initialize the core application."""
    app = Flask(__name__, instance_relative_config=False)
    app.config.from_object(config.Config())
    csrf.init_app(app)
    bootstrap = Bootstrap5(app)

    # Initialize Plugins

    with app.app_context():
        blueprints = [bp_homepage, authentication]
        # Register Blueprints
        for blueprint in blueprints:
            app.register_blueprint(blueprint)
        return app
