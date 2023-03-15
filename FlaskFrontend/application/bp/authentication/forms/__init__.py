

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

class LikeButton(FlaskForm):
    like = SubmitField('Like')

    
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

class IngredientForm(FlaskForm):
    name = StringField('Name')
    selected = BooleanField('Selected')

class RecipeForm(FlaskForm):
    name = StringField('Name')
    ingredients = FieldList(FormField(IngredientForm), min_entries=1)

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

class EventsForm(FlaskForm):
    class Meta:
        csrf = False
    
    search = StringField('Search', validators=[DataRequired()])
    city = StringField('City', validators=[DataRequired()])
    state = StringField('State', validators=[DataRequired()])
    submit = SubmitField("search for events")

class IngredientsForm(FlaskForm):
    class SpiritCategory(FlaskForm):
        light_rum = IntegerField('Light rum', validators=[DataRequired()])
        dark_rum = IntegerField('Dark rum', validators=[DataRequired()])
        scotch = IntegerField('Scotch', validators=[DataRequired()])
        brandy = IntegerField('Brandy', validators=[DataRequired()])
        lemon_vodka = IntegerField('Lemon vodka', validators=[DataRequired()])
        blended_whiskey = IntegerField('Blended whiskey', validators=[DataRequired()])
        tequila = IntegerField('Tequila', validators=[DataRequired()])
        vodka = IntegerField('Vodka', validators=[DataRequired()])
        anejo_rum = IntegerField('Añejo rum', validators=[DataRequired()])
        irish_whiskey = IntegerField('Irish whiskey', validators=[DataRequired()])
        bourbon = IntegerField('Bourbon', validators=[DataRequired()])
        cognac = IntegerField('Cognac', validators=[DataRequired()])
        sloe_gin = IntegerField('Sloe gin', validators=[DataRequired()])
        peach_vodka = IntegerField('Peach Vodka', validators=[DataRequired()])

    class LiqueurCategory(FlaskForm):
        applejack = IntegerField('Applejack', validators=[DataRequired()])
        sweet_vermouth = IntegerField('Sweet Vermouth', validators=[DataRequired()])
        strawberry_schnapps = IntegerField('Strawberry schnapps', validators=[DataRequired()])
        apricot_brandy = IntegerField('Apricot brandy', validators=[DataRequired()])
        triple_sec = IntegerField('Triple sec', validators=[DataRequired()])
        southern_comfort = IntegerField('Southern Comfort', validators=[DataRequired()])
        amaretto = IntegerField('Amaretto', validators=[DataRequired()])
        coffee_liqueur = IntegerField('Coffee liqueur', validators=[DataRequired()])
        kahlua = IntegerField('Kahlua', validators=[DataRequired()])
        dubonnet_rouge = IntegerField('Dubonnet Rouge', validators=[DataRequired()])
        cherry_brandy = IntegerField('Cherry brandy', validators=[DataRequired()])
        creme_de_cacao = IntegerField('Creme de Cacao', validators=[DataRequired()])
        grenadine = IntegerField('Grenadine', validators=[DataRequired()])
        port = IntegerField('Port', validators=[DataRequired()])
        coffee_brandy = IntegerField('Coffee brandy', validators=[DataRequired()])
        ricard = IntegerField('Ricard', validators=[DataRequired()])
        sherry = IntegerField('Sherry', validators=[DataRequired()])
        galliano = IntegerField('Galliano', validators=[DataRequired()])

    class MixerCategory(FlaskForm):
        tea = IntegerField('Tea', validators=[DataRequired()])
        champagne = IntegerField('Champagne', validators=[DataRequired()])
        carbonated_water = IntegerField('Carbonated water', validators=[DataRequired()])
        watermelon = IntegerField('Watermelon', validators=[DataRequired()])
        lime_juice = IntegerField('Lime juice', validators=[DataRequired()])
        apple_juice = IntegerField('Apple juice', validators=[DataRequired()])
        pineapple_juice = IntegerField('Pineapple juice', validators=[DataRequired()])
        lemon_juice  = IntegerField('Lemon Juice', validators=[DataRequired()])
        sugar_syrup = IntegerField('Sugar syrup', validators=[DataRequired()])
        tomato_juice = IntegerField('Tomato juice', validators=[DataRequired()])
        strawberries = IntegerField('Strawberries', validators=[DataRequired()])
        yoghurt = IntegerField('Yoghurt', validators=[DataRequired()])
        mango = IntegerField('Mango', validators=[DataRequired()])
        ginger = IntegerField('Ginger', validators=[DataRequired()])
        lime = IntegerField('Lime', validators=[DataRequired()])
        cantaloupe = IntegerField('Cantaloupe', validators=[DataRequired()])
        berries = IntegerField('Berries', validators=[DataRequired()])
        grapes = IntegerField('Grapes', validators=[DataRequired()])
        kiwi = IntegerField('Kiwi', validators=[DataRequired()])
        cocoa_powder = IntegerField('Cocoa powder', validators=[DataRequired()])
        chocolate = IntegerField('Chocolate', validators=[DataRequired()])
        heavy_cream = IntegerField('Heavy cream', validators=[DataRequired()])

    spirits = FieldList(FormField(SpiritCategory), label='Spirits')
    liqueurs = FieldList(FormField(LiqueurCategory), label='Liqueurs')
    mixers = FieldList(FormField(MixerCategory), label='Mixers')
    submit = SubmitField('Submit')

