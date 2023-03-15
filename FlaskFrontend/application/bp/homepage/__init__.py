
import json
from flask import Blueprint, jsonify, render_template, request
#from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton, EventsForm
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
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
        
        data=get_data(searchtype)
      
    if(data):   
        return render_template('drinkwithyoureyes.html', data=data)
    else:
        return "something broke"


@bp_homepage.route('/data')
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
   
 
@bp_homepage.route('/like', methods=['POST'])
def like():
    data = request.json
    data_index = data['data_index']
    drinkData = data['drinkData'][data_index]
    print(drinkData)
    return drinkData
  
    # Process the data and return a response    


@bp_homepage.route('/apiSearch', methods=['GET', 'POST'])
def apiSearch():
    form = SearchForm()
    like=LikeButton()
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
    form = IngredientsForm()
    if form.validate_on_submit():
        pass
    return render_template('create_cocktail.html', form=form)

@bp_homepage.route('/events', methods=['GET', 'POST'])
def events():
    form = EventsForm()
    if form.validate_on_submit():
        search = request.form['search']
        city = str(request.form['city'])
        state = str(request.form['state'])
        location = city +', '+ state

        if search and location:
            client = RabbitMQClient('testServer')
            try:
                request_dict = {
                    'type': 'API_CALL',
                    'key': {
                        'type': 'GoogleEventSearch',
                        'operation': 's',
                        'searchTerm': search,
                        'location' : location
                    }
                }
                response = client.send_request(request_dict)
                return jsonify(response)    
            except Exception as e:
                print(str(e))
    else:
        response = []
    if form.validate_on_submit():
        pass
    return render_template('events.html', form=form)

