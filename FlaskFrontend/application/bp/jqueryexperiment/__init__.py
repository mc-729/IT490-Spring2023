
import random
import time
import json
from flask import Blueprint, jsonify, render_template, request
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_jqueryExample = Blueprint('jqueryExample', __name__, template_folder='templates')


@bp_jqueryExample .route("/jquerytest")
def jquerytestFunct():
   
    return render_template("jquerytest.html")

@bp_jqueryExample .route("/dataJS")
def dataJS():
    randomnum=random.random()
    return str(randomnum)

@bp_jqueryExample .route("/update")
def update():
    return render_template("update.html")