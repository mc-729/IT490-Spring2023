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

bp_editRecipe = Blueprint('editRecipe', __name__, template_folder='templates')




from flask import request
from flask import request, jsonify, redirect, url_for, session
from flask import request, jsonify, redirect, url_for, session

@bp_editRecipe.route('/editRecipe', methods=['POST'])
def editrecipe():

    if all([key in request.form for key in ['strCategory', 'strDrink', 'strDrinkThumb', 'strGlass', 'strInstructions']]):
        ingredients = []
        index = 1
        while True:  # Loop until break
            ingredient_key = f'ingredients-{index}-ingredient'
            measurement_key = f'ingredients-{index}-measurement'
            measurement_type_key = f'ingredients-{index}-measurement_type'
            print("we made it here" + str(index) )
            if all([key in request.form for key in [ingredient_key, measurement_key, measurement_type_key]]):
                if request.form[measurement_key] !="":
                    ingredients.append({
                    'measurement': request.form[measurement_key],
                    'ingredient': request.form[ingredient_key],
                    'measurement_type': request.form[measurement_type_key]
                })
                index += 1
            elif index<100:
                 index += 1
                
            else:
                break  # Break the loop if any key is not present

        if ingredients:
            alcoholic=False
            if "strAlcoholic" in request.form: alcoholic=True
            recipe = {
                "strAlcoholic":  alcoholic,
                "strCategory": request.form['strCategory'],
                "strDrink": request.form['strDrink'],
                "strDrinkThumb": request.form['strDrinkThumb'],
                "strGlass": request.form['strGlass'],
                "strInstructions": request.form['strInstructions'],
                'id':request.form['id'],
                "username": session['username']
            }
            for i, ingredient in enumerate(ingredients, start=1):
                ingredientName = "strIngredient" + str(i)
                measurementName = "strMeasure" + str(i)
                recipe[ingredientName] = ingredient['ingredient']
                recipe[measurementName] = str(ingredient['measurement']) + " " + ingredient['measurement_type']
                
            request_dict = {
            'type': 'EditUserRecipe',
            'recipeID':request.form['id'],
            'changes':recipe,
            'sessionid': session['sessionID'],
            'drinkNane':request.form['strDrink']
            
        }
                
            client = RabbitMQClient('testServer')
            response = client.send_request(request_dict)
            if(response):
               
                print(response)
                    
                flash(f'you have successfully updated the recipe {recipe.get("strDrink")}','success')
            
        else:
              flash('You are missing data.', 'danger')
    
    return redirect(url_for('myliquorcabinet.liquorcabinet'))
