
import json
from flask import Blueprint, jsonify, render_template, request
from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_drinkwithyoureyes = Blueprint('drinkwithyoureyes', __name__, template_folder='templates')



@bp_drinkwithyoureyes.route('/drinkwithyoureyes', methods=['GET','POST'])
def drinkwithyoureyes():
    data = {}
    searchtype = 'Random10Cocktails'

    if searchtype:
        
        data=get_data(searchtype)
      
    if(data):   
        return render_template('drinkwithyoureyes.html', data=data)
    else:
        return "something broke"


@bp_drinkwithyoureyes.route('/data')
def get_data(searchtype):
    client = RabbitMQClient('testServer')
    request_dict = {
            'type': 'API_CALL',
            'key': {
            'type': searchtype,
                }
            }
      
    
    response = client.send_request(request_dict)
    response= json.loads(json.loads(response))[0]
    response= json.loads(response)["drinks"]
    return response
   
 

