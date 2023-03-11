import ast
import json
from flask import Blueprint, jsonify, render_template, request
import pika
from flask_modals import render_template_modal
from django.views.decorators.csrf import csrf_exempt
from application.bp.authentication.forms import SearchForm , CocktailForm
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from flask_paginate import Pagination, get_page_parameter
from flask_paginate import Pagination, get_page_args
bp_homepage = Blueprint('homepage', __name__, template_folder='templates')


@bp_homepage.route('/')
def homepage():
    return render_template('homepage.html')


@bp_homepage.route('/about')
def about():
    return render_template('about.html')


@bp_homepage.route('/drinkwithyoureyes', methods=['GET','POST'])
def drinkwithyoureyes():
    data = False
    searchtype = 'Random10Cocktails'

    if searchtype:
        client = RabbitMQClient('APIServer')
        request_dict = {
            'type': 'API_CALL',
            'key': {
            'type': searchtype,
                }
            }
      
        data=get_data(searchtype)
      
    if(data):   
        return render_template('drinkwithyoureyes.html', data=data)
    else:
        return "something broke"

PER_PAGE = 3



@bp_homepage.route('/data')
def get_data(searchtype):
    
    client = RabbitMQClient('APIServer')
    request2={ 'type': searchtype} 
    response = client.send_request(request2)
 
    response= json.loads(json.loads(response))["drinks"]
    return response
 
    
@bp_homepage.route('/apiSearch', methods=['GET', 'POST'])




@bp_homepage.route('/apiSearch', methods=['GET', 'POST'])
def apiSearch():
    form = SearchForm()
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

            try:
                request2={ 'type': searchtype,
                    'operation': 's',
                    'searchTerm': searchTerm}            
                response = client.send_request(request_dict)
             
                response= json.loads(json.loads(response))[0]
                response= json.loads(response)["drinks"]
            except Exception as e:

                print(str(e))
        else:
            response = []

    else:
        response = []

  

    return render_template('apiSearch.html', form=form, data=response)


@bp_homepage.route('/create_cocktail', methods=['GET', 'POST'])
def create_cocktail():
    form = CocktailForm()
    if form.validate_on_submit():
        # Do something with the form data, such as saving to a database
        pass
    return render_template('create_cocktail.html', form=form)