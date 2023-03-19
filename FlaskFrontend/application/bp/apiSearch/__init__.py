
import ast
import json
from flask import Blueprint, flash, jsonify, redirect, render_template, request, session, url_for
#from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from application.jsonPgaination.JSONPagination import JSONPagination
bp_apiSearch = Blueprint('apiSearch', __name__, template_folder='templates')



    # Process the data and return a response    
@bp_apiSearch.route('/sendDrinkData', methods=['GET','POST'])
def sendDrinkData():
     drink_data = request.get_json()
    
     print(type(drink_data))
     
     drink= json.dumps(drink_data)
     drink=json.loads(drink)
     drinkName=ast.literal_eval(drink)["strDrink"]
     
     
   
     client = RabbitMQClient('testServer')
     request_dict = {
                'type': 'like',
                
                    'sessionID': session['sessionID'],
                    'drink': drink_data,
                    'drinkName':drinkName
                   


                
            }

     print(drink_data)
     response = client.send_request(request_dict)
     if(response):
        response = {"status": "success", "message": "Data received successfully."}
      
     else:
         response = {"status": "error", "message": "something went wrong"}
    
     return jsonify(response)

    # Return a response indicating the request was successful
    



@bp_apiSearch.route('/apiSearch', methods=['GET', 'POST'])
def apiSearch():
    form = SearchForm()
    like = LikeButton()

    paginated_response = {}
    page = int(request.args.get('page', 1))
    pagination = None

    if form.validate_on_submit() and form.ans.data and form.searchValue.data:
        session['searchtype'] = request.form['ans']
        session['searchTerm'] = request.form['searchValue']
        return redirect(url_for('apiSearch.apiSearch', ans=session['searchtype'], searchValue=session['searchTerm'], page=1))

    if 'searchtype' in session and 'searchTerm' in session:
        searchtype = session['searchtype']
        searchTerm = session['searchTerm']

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
                request2 = {'type': searchtype,
                            'operation': 's',
                            'searchTerm': searchTerm}
                response = client.send_request(request_dict)
                
                #return response
                response = json.loads(response)
                print(response.keys())
                #response = response["drinks"]


                likes_list = response["Likes"]
                print(likes_list)
                drinkList = response["drinks"]
                drinkList = json.loads(drinkList)[0]
                drinkList = drinkList['drinks']
                #return drinkList
                #grabbing ratings

                for drink in drinkList:
                    for like in likes_list:
                        for key, value in like.items():
                            if  key == drink["strDrink"]:
                                drinkName=drink["strDrink"]
                                
                                #drink.update(likeDict)
                                #drink['likes'] = like[drinkName]
                                print(value)
                                drink.update({"likes":value})
                                #if drink['strDrink'] in likes_list[0][drink]:
                                #drink.update(likes_list[drink])
                print("we made it to pagination \n")
                #print(json.dumps(drinkList, indent=2)
                
                #print(json.dumps(pageDrinkList, indent=2))
                page = request.args.get('page', 1, type=int)
                per_page = 10  # Change this to the desired number of items per page
                pagination = JSONPagination(drinkList, page, per_page)
                paginated_response = pagination.get_page_items()

            except Exception as e:
                print('Something went wrong in API search : '+ str(e))

    return render_template('apiSearch.html', form=form, data=paginated_response, like=like, pagination=pagination)







