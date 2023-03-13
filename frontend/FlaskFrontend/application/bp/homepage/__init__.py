import ast
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

PER_PAGE = 3

@bp_homepage. route('/myliquorcabinet')
def myliquorcabinet():
    return render_template('myliquorcabinet.html')

@bp_homepage.route('/data')
def get_data(data):
    
    data = json.loads(data)
    # Get the current page number from the request arguments
 
    # Return the subset of S

    return data[0]["drinks"]
@bp_homepage.route('/apiSearch', methods=['GET', 'POST'])
@csrf_exempt
def apiSearch():
    form = SearchForm()
    data = False
    if form.validate_on_submit():
        searchtype = request.form['ans']
        searchTerm = request.form['searchValue']

        if searchtype and searchTerm:
            client = RabbitMQClient('testServer')
            request_dict = {
                'type': 'API_CALL',
                'key': {
                    'type': searchtype,
                    'operation': 's',
                    'searchTerm': searchTerm
                }
            }
            response = client.send_request(request_dict)
            response= json.loads(json.loads(response))[0]
            response=json.loads(response)["drinks"]
          
            data=response
           
    return render_template('apiSearch.html', form=form,data=data)