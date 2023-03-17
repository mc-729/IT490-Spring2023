import json
from flask import Blueprint, jsonify, render_template, request, session
#from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton, EventsForm, submitBtn
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_liquorcabinet = Blueprint('myliquorcabinet', __name__, template_folder='templates')

@bp_liquorcabinet.route('/liquorcabinet', methods=['GET', 'POST'])
def liquorcabinet():
   
    client = RabbitMQClient('testServer')
    request_dict = {
                'type': 'retrieveRecipes',
                
                    'sessionID': session['sessionID'],
                    
                
            }

 
    response = client.send_request(request_dict)
    response = json.loads(json.loads(response))[0]
    response = json.loads(response)

    form=LikeButton
    return render_template('myliquorcabinet.html', form=form)