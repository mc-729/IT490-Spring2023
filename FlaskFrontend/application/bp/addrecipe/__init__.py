from functools import wraps
import uuid
from flask import Blueprint, jsonify,render_template, redirect, url_for, flash, request

from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from flask import session
from application.bp.authentication.forms import RecipeForm
from wtforms import validators
from wtforms.fields import *

from flask_wtf import FlaskForm
from wtforms import StringField, PasswordField, SubmitField, HiddenField
from wtforms.validators import DataRequired, Email, EqualTo

bp_addrecipe = Blueprint('addrecipe', __name__, template_folder='templates')




@bp_addrecipe.route('/addrecipe', methods=['GET', 'POST'])
def addrecipe():
    form = RecipeForm()

    # This will check if the form is submitted and all validators pass
    # Check if all six fields are set
    if form.is_submitted():
      
        if all([key in request.form for key in ['strCategory', 'strDrink', 'strDrinkThumb', 'strGlass', 'strInstructions']]):
          
            print( f'drink name -{form.strDrink.data}- Category {form.strCategory.data}')
            
            # Initialize an empty list to hold ingredients
            ingredients = []
            index=0
            uid = str(uuid.uuid4())
            # Check each Ingredient_Form for complete data
            for i in range(15, 29):
                 ingredient_key = f'ingredients-{i}-ingredient'
                 measurement_key = f'ingredients-{i}-measurement'
                 measurement_type_key = f'ingredients-{i}-measurement_type'
                 if request.form[measurement_key] !="": 
                    # If all fields in the Ingredient_Form are filled, append it to the ingredients list
                    print("we made it past ingredient if")
                    ingredients.append({
                        'measurement': request.form[measurement_key],
                        'ingredient': request.form[ ingredient_key],
                        'measurement_type':request.form[measurement_type_key],
                    })
            
            # Only proceed if at least one ingredient form was completely filled
            if ingredients:
                alcoholic=False
                if "strAlcoholic" in request.form: alcoholic=True
                index=0
                recipe = {
                    "strAlcoholic": alcoholic,
                    "strCategory": request.form["strCategory"],
                    "strDrink": request.form["strDrink"],
                    "strDrinkThumb": request.form["strDrinkThumb"],
                    "strGlass": request.form["strGlass"],
                    "strInstructions": request.form["strInstructions"],
                    "idDrink":(uid+request.form["strDrink"]).replace(" ", ""),
                    "username":session['username']
                }
                for ingredient in ingredients:
                    index+=1
                    ingredientName="strIngredient"+str(index)
                    measurementName="strMeasure"+str(index)
                    print(ingredientName +"  "+ measurementName)
                    recipe[ingredientName]=ingredient['ingredient']
                    recipe[measurementName]=str(ingredient['measurement'])+" "+ingredient['measurement_type']
                 
            
                
               
              
                request_dict = {
            'type': 'addRecipe',
            
            'recipe':recipe,
            'drink_name': recipe['strDrink'],
            'Username':   session['username'],
            'sessionid': session['sessionID'],
        }
                
                client = RabbitMQClient('testServer')
                response = client.send_request(request_dict)
                if(response):
                    flash('You have made it to rabbit.', 'success')
                    print(response)
                    
                

                
        else:
            flash('You are missing data.', 'danger')
    
    return render_template('addrecipe.html', form=form)