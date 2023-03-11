

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
    def __init__(self, first_name_placeholder=None, last_name_placeholder=None, username_placeholder=None, email_placeholder=None):
        super(EditProfileForm, self).__init__()
        self.first_name.render_kw = {"placeholder": first_name_placeholder} if first_name_placeholder else {}
        self.last_name.render_kw = {"placeholder": last_name_placeholder} if last_name_placeholder else {}
        self.username.render_kw = {"placeholder": username_placeholder} if username_placeholder else {}
        self.email.render_kw = {"placeholder": email_placeholder} if email_placeholder else {}


    
    first_name = StringField("First Name")
    last_name = StringField("Last Name")
    email = StringField("Email")
    username = StringField("Username")
    old_password = PasswordField("Old Password")
    new_password = PasswordField("New Password", validators=[EqualTo("confirm_password", message="Passwords must match")])
    confirm_password = PasswordField("Confirm Password")
    submit = SubmitField("Save Changes")


class SearchForm(FlaskForm):
    class Meta:
        csrf = False
    
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
class CocktailForm(FlaskForm):
    class Meta:
        csrf = False
    
    alcohol_type = SelectField('Select Alcohol Type', choices=[('Vodka', 'Vodka'), ('Gin', 'Gin'), ('Rum', 'Rum'), ('Tequila', 'Tequila'), ('Whiskey', 'Whiskey'), ('Brandy', 'Brandy'), ('Cognac', 'Cognac'), ('Other', 'Other')], validators=[DataRequired()])
    ingredients = [
        ('Bitters', 'Bitters'),
        ('Simple syrup', 'Simple syrup'),
        ('Grenadine', 'Grenadine'),
        ('Lime juice', 'Lime juice'),
        ('Lemon juice', 'Lemon juice'),
        ('Orange juice', 'Orange juice'),
        ('Pineapple juice', 'Pineapple juice'),
        ('Grapefruit juice', 'Grapefruit juice'),
        ('Cranberry juice', 'Cranberry juice'),
        ('Tomato juice', 'Tomato juice'),
        ('Club soda', 'Club soda'),
        ('Tonic water', 'Tonic water'),
        ('Ginger beer', 'Ginger beer'),
        ('Cola', 'Cola')
    ]
    ingredient_choices = [ (ingredient[0], ingredient[1]) for ingredient in ingredients ]
    ingredient_checkboxes = [ BooleanField(description=choice, validators=[DataRequired()]) for choice in ingredient_choices ]
    name = StringField('Name', validators=[DataRequired()])
    submit = SubmitField('Save')