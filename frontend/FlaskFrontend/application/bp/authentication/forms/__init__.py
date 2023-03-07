

from wtforms import validators
from wtforms.fields import *

from flask_wtf import FlaskForm
from wtforms import StringField, PasswordField, SubmitField
from wtforms.validators import DataRequired, Email, EqualTo

class LoginForm(FlaskForm):
    email = StringField('Email', [
        validators.DataRequired()
    ])

    password = PasswordField('Password', [
        validators.DataRequired(),
        validators.length(min=6, max=35)
    ])
    submit = SubmitField()


class RegisterForm(FlaskForm):
    username = StringField('Username', validators=[DataRequired()])
    email = StringField('Email', validators=[DataRequired(), Email()])
    password = PasswordField('Password',
                             validators=[DataRequired(), EqualTo('confirm_password', message='Passwords must match')])
    confirm_password = PasswordField('Confirm Password', validators=[DataRequired()])
    fname = StringField('First Name', validators=[DataRequired()])
    lname = StringField('Last Name', validators=[DataRequired()])
    submit = SubmitField('Register')

class EditProfileForm(FlaskForm):
    first_name = StringField("First Name")
    last_name = StringField("Last Name")
    email = StringField("Email")
    username = StringField("Username")
    old_password = PasswordField("Old Password")
    new_password = PasswordField("New Password", validators=[EqualTo("confirm_password", message="Passwords must match")])
    confirm_password = PasswordField("Confirm Password")
    submit = SubmitField("Save Changes")

class SearchForm(FlaskForm):
    ans = RadioField('Search Type', choices=[
        ('SearchByName', 'Search By Name'),
        ('SearchBySingleIngredient', 'Search By Ingredient'),
        ('GetCocktailDetailsByID', 'Search by ID'),
        ('Random10Cocktails', 'Random 10 Cocktails'),
        ('FilterByCategory', 'Filter by Category'),
        ('ListIngredients', 'List Ingredients'),
        ('SearchIngredientInfo', 'Search Ingredients Info')
    ], validators=[DataRequired()])
    searchValue = StringField('Search Value', validators=[DataRequired()])
    submit = SubmitField("search for drinks")