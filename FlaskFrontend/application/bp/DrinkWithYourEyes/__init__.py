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
    like = LikeButton()

    searchtype = 'SearchByName'
    searchTerm = random.choice('abcdefghijklmnopqrstuvwyxz')
    sessionID=None
    if 'sessionID' in session: sessionID=session['sessionID']
    if searchtype:
        client = RabbitMQClient('testServer')
        request_dict = {
            'type': 'API_CALL',
                'key': {
                    'type': searchtype,
                    'operation': 's',
                    'searchTerm': searchTerm
                },
                'loginStatus':sessionID
            }           
        paginated_data , pagination = get_data()
        objLen = len(data)
       
        response = client.send_request(request_dict)
             
      
        response= json.loads(response)
        print(str(len(response))+"main funct")
   # if searchtype:
        
   #     data=get_data(searchtype)
   #     objLen = len(data)
      
    if(paginated_data):   
        return render_template('drinkwithyoureyes.html', data=paginated_data, pagination=pagination ,like=like)
    else:

        client = RabbitMQClient('logServer')
        client.publish("Front end: DrinkWithYourEyes could not render template" )

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
    sessionID=None
    if 'sessionID' in session: sessionID=session['sessionID']

    client = RabbitMQClient('testServer')
    request_dict = {
            'type': 'API_CALL',
                'key': {
                    'type': searchtype,
                    'operation': 's',
                    'searchTerm': searchTerm
                },
                 'loginStatus':sessionID
            }   
    response = client.send_request(request_dict)
    response= json.loads(response)
    print(str(len(response))+"get data")
    page = request.args.get('page', 1, type=int)
    per_page = 10  # Change this to the desired number of items per page
    pagination = JSONPagination(response, page, per_page)
    paginated_data = pagination.get_page_items()
    print(len(response))

    
    return paginated_data,pagination

@bp_drinkwithyoureyes.route('/refreshOptions', methods=['GET', 'POST'])
def your_view_function():
    like = LikeButton()

    searchtype = 'SearchByName'
    searchTerm = random.choice('abcdefghijklmnopqrstuvwyxz')
    session['searchtype'] = searchtype
    session['searchterm'] = searchTerm
    page = request.args.get('page', 1, type=int)

    pagination = None

    if('searchtype' in session and 'searchterm' in session):
        sessionID=None
        if 'sessionID' in session: sessionID=session['sessionID']
        
        client = RabbitMQClient('testServer')
        request_dict = {
                'type': 'API_CALL',
                'key': {
                    'type': searchtype,
                    'operation': 's',
                    'searchTerm': searchTerm
                },
                'loginStatus':sessionID
            }
        response = client.send_request(request_dict)
   
        response = json.loads(response)
        total=int(len(response)/10)
        print(str(total)+"refresh and total is: "+str(len(response)) )
        randomNum=str(random.randrange(total))
        pagination = JSONPagination(response, page=page, per_page=10)
        paginated_response = pagination.get_page_items()
        

    return render_template('drinkwithyoureyes.html',data = paginated_response ,pagination=pagination,like=like, page=page,randNum=randomNum)


 

