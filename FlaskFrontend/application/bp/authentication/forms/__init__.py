from wtforms import validators
from wtforms.fields import *

from flask_wtf import FlaskForm
from wtforms import Form,StringField, PasswordField, SubmitField, HiddenField, FormField, FieldList
from wtforms.validators import DataRequired, Email, EqualTo,  NumberRange, Optional

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
    class Meta:
        csrf = False
    like=StringField("")
    drinks=StringField("")
    submit= SubmitField('Like')

    
class RegisterForm(FlaskForm):
    username = StringField('Username', validators=[DataRequired()])
    email = StringField('Email', validators=[DataRequired(), Email()])
    password = PasswordField('Password',
                             validators=[DataRequired(), EqualTo('confirm_password', message='Passwords must match')])
    confirm_password = PasswordField('Confirm Password', validators=[DataRequired()])
    fname = StringField('First Name', validators=[DataRequired()])
    lname = StringField('Last Name', validators=[DataRequired()])
    city = StringField('City', validators=[DataRequired()])
    state = StringField('State', validators=[DataRequired()])
    submit = SubmitField('Register')

class EditProfileForm(FlaskForm):
    def __init__(self, first_name_placeholder=None, last_name_placeholder=None, city_placeholder=None, state_placeholder=None, username_placeholder=None, email_placeholder=None):
        super(EditProfileForm, self).__init__()
        self.first_name.render_kw = {"placeholder": first_name_placeholder} if first_name_placeholder else {}
        self.last_name.render_kw = {"placeholder": last_name_placeholder} if last_name_placeholder else {}
        self.city.render_kw = {"placeholder": city_placeholder} if city_placeholder else {}
        self.state.render_kw = {"placeholder": state_placeholder} if state_placeholder else {}
        self.username.render_kw = {"placeholder": username_placeholder} if username_placeholder else {}
        self.email.render_kw = {"placeholder": email_placeholder} if email_placeholder else {}


    
    first_name = StringField("First Name")
    last_name = StringField("Last Name")
    city = StringField("City")
    state = StringField("State")
    email = StringField("Email")
    username = StringField("Username")
    old_password = PasswordField("Old Password")
    new_password = PasswordField("New Password", validators=[EqualTo("confirm_password", message="Passwords must match")])
    confirm_password = PasswordField("Confirm Password")
    submit = SubmitField("Save Changes")

class IngredientForm(FlaskForm):
    name = StringField('Name')
    selected = BooleanField('Selected')
class Ingredient_Form(FlaskForm):
   

 
    ingredient = SelectField('Ingredient', choices=[(ingredient, ingredient) for ingredient in [
        "Light rum",
        "Applejack",
        "Gin",
        "Dark rum",
        "Sweet Vermouth",
        "Strawberry schnapps",
        "Scotch",
        "Apricot brandy",
        "Triple sec",
        "Southern Comfort",
        "Orange bitters",
        "Brandy",
        "Lemon vodka",
        "Blended whiskey",
        "Dry Vermouth",
        "Amaretto",
        "Tea",
        "Champagne",
        "Coffee liqueur",
        "Bourbon",
        "Tequila",
        "Vodka",
        "Anejo rum",
        "Bitters",
        "Sugar",
        "Kahlua",
        "demerara Sugar",
        "Dubonnet Rouge",
        "Watermelon",
        "Lime juice",
        "Irish whiskey",
        "Apple brandy",
        "Carbonated water",
        "Cherry brandy",
        "Creme de Cacao",
        "Grenadine",
        "Port",
        "Coffee brandy",
        "Red wine",
        "Rum",
        "Grapefruit juice",
        "Ricard",
        "Sherry",
        "Cognac",
        "Sloe gin",
        "Apple juice",
        "Pineapple juice",
        "Lemon juice",
        "Sugar syrup",
        "Milk",
        "Strawberries",
        "Chocolate syrup",
        "Yoghurt",
        "Mango",
        "Ginger",
        "Lime",
        "Cantaloupe",
        "Berries",
        "Grapes",
        "Kiwi",
        "Tomato juice",
        "Cocoa powder",
        "Chocolate",
        "Heavy cream",
        "Galliano",
        "Peach Vodka"
    ]], coerce=str)
    measurement = IntegerField('Measurement', validators=[Optional(), NumberRange(min=1)])
    measurement_type = SelectField('Measurement Type', choices=[
        ('ml', 'ml'),
        ('cl', 'cl'),
        ('oz', 'oz'),
        ('cup', 'cup'),
        ('tbsp', 'tbsp'),
        ('tsp', 'tsp'),
        ('dash', 'dash'),
        ('pinch', 'pinch'),
        ('part', 'part'),
        ('piece', 'piece'),
        ('slice', 'slice'),
        ('sprig', 'sprig'),
        ('leaf', 'leaf'),
        ('can', 'can'),
        ('bottle', 'bottle'),
        ('drop', 'drop'),
    ])
class RecipeForm(FlaskForm):
    # assuming that ingredients is a FieldList of FormFields
  
    def __init__(self, strAlcoholic_default=None, strCategory_default=None, strDrink_default=None,
                 strDrinkThumb_default=None, strGlass_default=None, strInstructions_default=None,
                 id_default=None):
        super(RecipeForm, self).__init__()

        if strAlcoholic_default is not None:
            self.strAlcoholic.default = strAlcoholic_default

        if strCategory_default is not None:
            self.strCategory.default = strCategory_default

        if strDrink_default is not None:
            self.strDrink.default = strDrink_default

        if strDrinkThumb_default is not None:
            self.strDrinkThumb.default = strDrinkThumb_default

        if strGlass_default is not None:
            self.strGlass.default = strGlass_default

        if strInstructions_default is not None:
            self.strInstructions.default = strInstructions_default
        if id_default is not None:
            self.id.default=id_default

       

        self.process() 
    
    strAlcoholic = BooleanField('Alcoholic',)
    strCategory = SelectField('Category', choices=[
        ('cocktail', 'Cocktail'),
        ('mixed_drink', 'Mixed Drink'),
        ('mocktail', 'Mocktail'),
        ('beer_cocktail', 'Beer Cocktail'),
        ('wine_cocktail', 'Wine Cocktail'),
        ('classic', 'Classic'),
        ('tiki', 'Tiki'),
        ('frozen', 'Frozen'),
        ],)
    strDrink = StringField('Cocktail Name',)
    strDrinkThumb = StringField('Drink Thumbnail',)
    strGlass = SelectField('Glass', choices=[
        ('cocktail', 'Cocktail Glass'),
        ('highball', 'Highball Glass'),
        ('collins', 'Collins Glass'),
        ('martini', 'Martini Glass'),
        ('margarita', 'Margarita/Coupette Glass'),
        ('old_fashioned', 'Old-Fashioned Glass'),
        ('hurricane', 'Hurricane Glass'),
        ('wine', 'Wine Glass'),
        ('shot', 'Shot Glass'),
        ('punch', 'Punch Bowl'),
        ('pint', 'Pint Glass'),
        ('mug', 'Beer Mug'),
        ],)
    strInstructions = TextAreaField('Instructions',)
    ingredients = FieldList(FormField(Ingredient_Form), min_entries=15, max_entries=15)
    id=HiddenField()


    submit = SubmitField("submit")
class SearchForm(FlaskForm):
    class Meta:
        csrf = False
    ans = RadioField('Search Type', choices=[
           ('searchByName', 'search By Name'),
        ('Feelin Lucky + Recommend', 'Feelin Lucky + Recommend'),
        ('Feeling Lucky', 'Feeling Lucky'),
        ('Recommend and Search by Name', 'Recommend and Search by Name')
        ])
    searchValue = StringField('Search Value')
    submit = SubmitField("search for drinks")

class EventsForm(FlaskForm):
    class Meta:
        csrf = False
    
    search = StringField('Search', validators=[DataRequired()])
    city = StringField('City', validators=[DataRequired()])
    state = StringField('State', validators=[DataRequired()])
    submit = SubmitField("search for events")
    submit2 = SubmitField("recommend events")

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


class submitBtn(FlaskForm):
     submit = SubmitField('Submit')