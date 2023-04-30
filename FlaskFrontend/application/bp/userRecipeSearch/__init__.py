from functools import wraps
from flask import Blueprint, jsonify,render_template, redirect, url_for, flash, request

from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from flask import session
from application.bp.authentication.forms import RecipeForm
from wtforms import validators
from wtforms.fields import *
import json
from flask_wtf import FlaskForm
from wtforms import StringField, PasswordField, SubmitField, HiddenField
from wtforms.validators import DataRequired, Email, EqualTo

bp_userRecipeSearch = Blueprint('userRecipeSearch', __name__, template_folder='templates')

@bp_userRecipeSearch.route('/userRecipeSearch', methods=['GET', 'POST'])
def userRecipeSearch():
      client = RabbitMQClient('testServer')
      request_dict={'type':'retrieveAllUserRecipes'}
      response = client.send_request(request_dict)
      print(type(response))
      response=json.loads(response)["drinkList"]


      RecipeList=[]
      for recipes in response:
            recipe=  json.loads(recipes["Recipe"])
            recipe['username']= recipes["Username"]
        
            RecipeList.append(recipe)
            
     
          

      return render_template('userRecipeSearch.html',data=RecipeList)