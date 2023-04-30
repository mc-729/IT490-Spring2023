
import ast
import json
import random

from flask import Blueprint, flash, jsonify, redirect, render_template, request, session, url_for


from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from application.jsonPgaination.JSONPagination import JSONPagination
bp_apiSearch = Blueprint('apiSearch', __name__, template_folder='templates')



    # Process the data and return a response    
@bp_apiSearch.route('/sendDrinkData', methods=['GET','POST'])
def sendDrinkData():
      action = request.args.get('action')
      drink_data = request.get_json()

      drink = json.dumps(drink_data)
      drink = json.loads(drink)
      drinkName = ast.literal_eval(drink)["strDrink"]

      client = RabbitMQClient('testServer')

      if action == 'like':
        request_dict = {
            'type': 'like',
            'sessionID': session['sessionID'],
            'drink': drink_data,
            'drinkName': drinkName
        }
      elif action == 'unlike':
        request_dict = {
            'type': 'deleteRecipe',
            'sessionID': session['sessionID'],
            'drinkName': drinkName
        }
    
       
      response = client.send_request(request_dict)
      if(response):
            response = {"status": "success", "message": "Data processed successfully."}
      else:
         client = RabbitMQClient('logServer')
         client.publish("Front end did not Receive drink data")
         response = {"status": "error", "message": "something went wrong"}
      return jsonify(response)
  
              
      




   

        # Return a response indicating the request was successful
    






def get_search_term(form):
    if 'sessionID' in session:
        match form.ans.data:
            case "Feelin Lucky + Recommend":
                session['searchTerm'] = random.choice('abcdefghijklmnopqrstuvwyxz')
                session['filter'] = True
            case "Feeling Lucky":
                session['searchTerm'] = random.choice('abcdefghijklmnopqrstuvwyxz')
                session['filter'] = False
            case "Recommend and Search by Name":
                session['filter'] = True
            case "searchByName":
                session['filter'] = False
            case _:
                flash("You have to be logged in to use the other search functions", "error")
                session['searchTerm'] = request.form['searchValue']
    else:
        match form.ans.data:
            case "searchByName":
                session['searchTerm'] = request.form['searchValue']
            case "Feeling Lucky":
                session['searchTerm'] = random.choice('abcdefghijklmnopqrstuvwyxz')
            case _:
                flash("You have to be logged in to use the other search functions", "error")



def get_request_dict(searchTerm, sessionID):
    request_dict = {
        'type': 'API_CALL',
        'key': {
            'type': 'SearchByName',
            'operation': 's',
            'searchTerm': searchTerm
        },
        'loginStatus': sessionID,
        'filterby': session.get('filter', False)
    }
    return request_dict

def get_paginated_response(client, request_dict):
    try:
        response = client.send_request(request_dict)
        response = json.loads(response)

        page = request.args.get('page', 1, type=int)
        per_page = 10  # Change this to the desired number of items per page
        pagination = JSONPagination(response, page, per_page)
        paginated_response = pagination.get_page_items()

        return paginated_response, pagination

    except Exception as e:
        log_error(client, str(e))
        return None, None

def log_error(client, error_message):
    request2 = {
        'type': "error",
        'service': "frontend",
        'message': "Something went wrong in API search : " + error_message
    }
    json.dumps(request2)
    client.publish(request2)
    print('Something went wrong in API search : ' + error_message)

@bp_apiSearch.route('/apiSearch', methods=['GET', 'POST'])
def apiSearch():
    form = SearchForm()
    like = LikeButton()

    paginated_response = {}
    pagination = None

    if form.validate_on_submit():
        get_search_term(form)
        return redirect(url_for('apiSearch.apiSearch', searchValue=session['searchTerm'], page=1))

    if 'searchTerm' in session:
        searchTerm = session['searchTerm']
        sessionID = session.get('sessionID', None)

        client = RabbitMQClient('testServer')
        request_dict = get_request_dict(searchTerm, sessionID)

        paginated_response, pagination = get_paginated_response(client, request_dict)

    return render_template('apiSearch.html', form=form, data=paginated_response, like=like, pagination=pagination)