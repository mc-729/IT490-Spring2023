
import ast
import json
from flask import Blueprint, flash, jsonify, render_template, request
from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_apiSearch = Blueprint('apiSearch', __name__, template_folder='templates')



    # Process the data and return a response    
@bp_apiSearch.route('/sendDrinkData', methods=['GET','POST'])
def sendDrinkData():
     drink_data = request.get_json()
    
     print(type(drink_data))
     

     drink_data = json.dumps(drink_data)
     drink_data=json.loads(drink_data)
     drinkName=ast.literal_eval(drink_data)["strDrink"]
     
     drink_data = json.dumps(drink_data)
     client = RabbitMQClient('testServer')
     request_dict = {
                'type': 'like',
                
                    'drinkName': drinkName,
                    'drink': drink_data
                
            }

     print(drink_data)
     response = client.send_request(request_dict)
     if(response):
        response = {"status": "success", "message": "Data received successfully."}
        flash("you have liked the recipe for %s"%drinkName, "success")
     else:
         response = {"status": "error", "message": "something went wrong"}
    
     return jsonify(response)

    # Return a response indicating the request was successful
    



@bp_apiSearch.route('/apiSearch', methods=['GET', 'POST'])
def apiSearch():
    form = SearchForm()
    like=LikeButton()
  
    response={}
   
    if form.validate_on_submit() and form.ans.data and form.searchValue.data: 
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

    
    return render_template('apiSearch.html', form=form, data=response,like=like)



