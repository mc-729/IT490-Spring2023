
import json
from flask import Blueprint, jsonify, render_template, request



from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_homepage = Blueprint('homepage', __name__, template_folder='templates')


@bp_homepage.route('/')
def homepage():
    return render_template('homepage.html')


@bp_homepage.route('/about')
def about():
    return render_template('about.html')


