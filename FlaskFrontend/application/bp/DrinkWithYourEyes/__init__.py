
import json
import random
from flask import Blueprint, jsonify, render_template, request, session
from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from application.jsonPgaination.JSONPagination import JSONPagination
bp_drinkwithyoureyes = Blueprint('drinkwithyoureyes', __name__, template_folder='templates')



@bp_drinkwithyoureyes.route('/drinkwithyoureyes', methods=['GET','POST'])
def drinkwithyoureyes():
    data = {}
    searchtype = 'SearchByName'
    searchTerm = random.choice('abcdefghijklmnopqrstuvwyxz')
    if searchtype:
        client = RabbitMQClient('testServer')
        request_dict = {
            'type': 'API_CALL',
                'key': {
                    'type': searchtype,
                    'operation': 's',
                    'searchTerm': searchTerm
                }
            }           
        paginated_data , pagination = get_data()
        objLen = len(data)
        response = client.send_request(request_dict)
             
        response= json.loads(json.loads(response))[0]
        response= json.loads(response)["drinks"]

   # if searchtype:
        
   #     data=get_data(searchtype)
   #     objLen = len(data)
      
    if(paginated_data):   
        return render_template('drinkwithyoureyes.html', data=paginated_data, pagination=pagination )
    else:
        return "something broke" 


@bp_drinkwithyoureyes.route('/data')
def get_data():
    client = RabbitMQClient('testServer')

    paginated_response = {}
    page = int(request.args.get('page', 1))
    pagination = None
      
    searchtype = 'SearchByName'
    searchTerm = random.choice('abcdefghijklmnopqrstuvwyxz')
    session['searchtype'] = searchtype
    session['searchterm'] = searchTerm

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
    response= json.loads(response)["drinks"]

    page = request.args.get('page', 1, type=int)
    per_page = 10  # Change this to the desired number of items per page
    pagination = JSONPagination(response, page, per_page)
    paginated_data = pagination.get_page_items()

    
    return paginated_data,pagination

@bp_drinkwithyoureyes.route('/refreshOptions', methods=['GET', 'POST'])   
def get_paginated_cocktails_data(page=0, items_per_page=10):
    all_data = get_data()  # Implement your own function to get all cocktails data
    start_index = page * items_per_page
    end_index = start_index + items_per_page
    paginated_data = all_data[start_index:end_index]
    return paginated_data
 

@bp_drinkwithyoureyes.route("/getCards")
def get_cards():

    page = int(request.args.get("page", 0))
    data = get_data(page)  # Implement your own function to get cocktails data
    return render_template("drinkwithyoureyes.html ", data=data)
 

