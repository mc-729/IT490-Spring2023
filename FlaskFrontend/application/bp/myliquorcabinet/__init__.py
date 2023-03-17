import ast
import json
from flask import Blueprint, jsonify, render_template, request, session
#from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton, EventsForm, submitBtn
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_liquorcabinet = Blueprint('myliquorcabinet', __name__, template_folder='templates')

@bp_liquorcabinet.route('/liquorcabinet', methods=['GET', 'POST'])
def liquorcabinet():
    data = {}

    client = RabbitMQClient('testServer')
    request_dict = {
                'type': 'retrieveRecipe',
                
                    'sessionID': session['sessionID'],
                    
                    
                
            }

    i=0
    response = client.send_request(request_dict)
    response=json.loads(response)
    RecipeList=list()
   
    for val in response:
         val2=val["Recipe"]
         val3=json.loads(val2)
         new_word=ast.literal_eval(val3)
         RecipeList.append(new_word)
    
    
   
    
    return render_template('myliquorcabinet.html',data=RecipeList)
