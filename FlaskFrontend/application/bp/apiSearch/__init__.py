
import json
from flask import Blueprint, jsonify, render_template, request
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_apiSearch = Blueprint('apiSearch', __name__, template_folder='templates')



    # Process the data and return a response    


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

    elif like.validate_on_submit(): 
            drink=jsonify(like.like.data)
           
            drinks={like.drinks.data}
            print(type(like.drinks.data))

            return like.drinks.data
           
            print(drink)
            
            return render_template('apiSearch.html', form=form, data=response,like=like)
    return render_template('apiSearch.html', form=form, data=response,like=like)


