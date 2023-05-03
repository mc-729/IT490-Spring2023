import ast
import json
from flask import Blueprint, flash, jsonify, redirect, render_template, request, session, url_for
#from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , Ingredient_Form, LikeButton, EventsForm,  RecipeForm
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from application.jsonPgaination.JSONPagination import JSONPagination
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
    RecipeList=list()
    UserRecipeList=list()
    response = client.send_request(request_dict)
    #response=json.loads(response)
    #return jsonify(response)
    UserRecipe=json.loads(response)["userRecipes"]

    RecipeResponseList=json.loads(response)["drinkList"]
    IngredientList= json.loads(response)["ingredients"]
    UserIng=json.loads(response)["userIngredients"]
    for thing in UserRecipe:
     
        recipe=json.loads(thing['Recipe'])
        recipe['id']=thing['id']
     
        UserRecipeList.append(recipe)
        
    MasterIngredients=[]
    for ingredient in IngredientList:
        MasterIngredients.append(ingredient["name"])
    
   
  
   
    for val in RecipeResponseList:
         val2=val["Recipe"]
         val3=json.loads(val2)
      
         new_word=ast.literal_eval(val3)
     
         RecipeList.append(new_word)
    edit_recipe_forms = []
    for recipe in UserRecipeList:
        ingredients_default = []
        form = RecipeForm(strAlcoholic_default= recipe.get('strAlcoholic'),
                          strCategory_default=recipe.get('strCategory'),
                          strDrink_default=recipe.get('strDrink'),
                          strDrinkThumb_default=recipe.get('strDrinkThumb') ,
                          strGlass_default=recipe.get('strGlass'),
                          strInstructions_default=recipe.get('strInstructions'),
                          id_default=recipe.get('id')
                    )
      
    # ... fill in the rest of the fields as necessary ...

    # Parse and fill the ingredients

              
     
        
        edit_recipe_forms.append(form)
     
    page = request.args.get('page', 1, type=int)
    per_page = 10  # Change this to the desired number of items per page
    pagination = JSONPagination(RecipeList, page, per_page)
    paginated_response = pagination.get_page_items()
    
    
   
 
    return render_template('myliquorcabinet.html',data=paginated_response,edit_recipe_forms=edit_recipe_forms ,userRecipe=UserRecipeList,MasterIngredients=MasterIngredients,like=like,UserIng=UserIng,pagination=pagination)

@bp_liquorcabinet.route('/submit_ingredient', methods=['GET', 'POST'])
def submit_ingredient():
     ingredient_data = request.get_json()
     print(ingredient_data)
     ingredient=ingredient_data["ingredient"]
     amount=ingredient_data["amount"]
     measurement=ingredient_data["measurement"]
     client = RabbitMQClient('testServer')


     request_dict = {
           'type' : 'updateMLC',
           'sessionID': session['sessionID'],
           'ingName' : ingredient,
           'amount' : amount,
           'measurementType' : measurement
     }

     if ingredient!="" and amount !="" and measurement !="":
            response = {"status": "success", "message": "Data received successfully."}
            data = client.send_request(request_dict)
     else:
            response = {"status": "failure", "message": "something went wrong."}
     return jsonify(response)



@bp_liquorcabinet.route('/deleteMyRecipe', methods=['GET', 'POST'])
def deleteMyRecipe():
    id= request.form.get('drinkID')
    print(id)
    client = RabbitMQClient('testServer')
    request_dict={'type':'DeleteUserRecipe','recipeID':id, 'sessionid':session['sessionID']}
    
    response = client.send_request(request_dict)
    if(response):
                flash(f'You have deleted the Recipe', 'success')
                print(response)
    return redirect(url_for('myliquorcabinet.liquorcabinet'))
