import json
from flask import Blueprint, jsonify, render_template, request
#from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton, EventsForm
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_myliquorcabinet = Blueprint('myliquorcabinet', __name__, template_folder='templates')

@bp_myliquorcabinet.route('/myliquorcabinet')
def myliquorcabinet():
    return render_template('myliquorcabinet.html')