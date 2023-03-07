import json
from flask import Blueprint, jsonify, render_template, request
import pika

from django.views.decorators.csrf import csrf_exempt
from application.bp.authentication.forms import SearchForm
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_homepage = Blueprint('homepage', __name__, template_folder='templates')


@bp_homepage.route('/')
def homepage():
    return render_template('homepage.html')


@bp_homepage.route('/about')
def about():
    return render_template('about.html')


@bp_homepage.route('/drinkwithyoureyes')
def drinkwithyoureyes():
    return render_template('drinkwithyoureyes.html')







@bp_homepage.route('/apiSearch', methods=['GET', 'POST'])
def apiSearch():
    form = SearchForm()
    if form.validate_on_submit():
        type = request.form['ans']
        searchTerm = request.form['searchValue']

        if type and searchTerm:
            client = RabbitMQClient('testServer')
            request_dict = {
                'type': 'API_CALL',
                'key': {
                    'type': type,
                    'operation': 's',
                    'searchTerm': searchTerm
                }
            }
            response = client.send_request(request_dict)
            obj = json.loads(response)
            if obj is not None: print(json.dumps(obj))
            return jsonify(obj)
    return render_template('apiSearch.html', form=form)