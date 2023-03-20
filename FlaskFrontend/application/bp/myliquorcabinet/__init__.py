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
    like = LikeButton()


    client = RabbitMQClient('testServer')
    request_dict = {
                'type': 'retrieveRecipe',
                
                    'sessionID': session['sessionID'],
                    
                    
                
            }

    i=0
    response = client.send_request(request_dict)


    RecipeResponseList=json.loads(response)["drinkList"]
    IngredientList= json.loads(response)["ingredients"]
    MasterIngredients=[]
    for ingredient in IngredientList:
        MasterIngredients.append(ingredient["name"])
    
   
    RecipeList=list()
   
    for val in RecipeResponseList:
         val2=val["Recipe"]
         val3=json.loads(val2)
         new_word=ast.literal_eval(val3)
         RecipeList.append(new_word)
    
    
   
    
    return render_template('myliquorcabinet.html',data=RecipeList,MasterIngredients=MasterIngredients,like=like)

@bp_liquorcabinet.route('/submit_ingredient', methods=['GET', 'POST'])
def submit_ingredient():
     ingredient_data = request.get_json()
     print(ingredient_data)
     ingredient=ingredient_data["ingredient"]
     amount=ingredient_data["amount"]
     measurement=ingredient_data["measurement"]
     if ingredient!="" and amount !="" and measurement !="":
             response = {"status": "success", "message": "Data received successfully."}
     else:
            response = {"status": "failure", "message": "something went wrong."}
    
     return jsonify(response)





