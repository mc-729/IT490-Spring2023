
import ast
import json
import random

from flask import Blueprint, flash, jsonify, redirect, render_template, request, session, url_for


from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from application.jsonPgaination.JSONPagination import JSONPagination
bp_singleRecipe = Blueprint('singleRecipe', __name__, template_folder='templates')



    # Process the data and return a response    


@bp_singleRecipe.route('/singleRecipe', methods=['GET', 'POST'])
def singleRecipe():
   

    like = LikeButton()


 
    searchTerm = request.args.get('searchValue')
    

    client = RabbitMQClient('testServer')
    request_dict = {
        'type': 'API_CALL',
        'key': {
            'type': 'SearchByName',
            'operation': 's',
            'searchTerm': searchTerm
        },
        'loginStatus':False,
        'filterby': session.get('filter', False)
    }
    response = client.send_request(request_dict)
    response = json.loads(response)
    
    print(type(response[0]))
 
    return render_template('singleRecipe.html', d=response[0],like=like)
@bp_singleRecipe.route('/UsersingleRecipe', methods=['GET', 'POST'])
def UsersingleRecipe():
   

    like = LikeButton()


 
    searchTerm = request.args.get('searchValue')
    print(searchTerm)
    

    client = RabbitMQClient('testServer')
    request_dict = {
        'type': 'retrieveUserRecipes',
        'id':searchTerm
     
    }
    response = client.send_request(request_dict)
    response = json.loads(response)
    response=response[0].get('Recipe')
    response=json.loads(response)
    
  
    
   
    return render_template('singleRecipe.html', d=response,like=like)