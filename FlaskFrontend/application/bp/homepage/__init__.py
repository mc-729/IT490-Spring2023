
import json
from flask import Blueprint, jsonify, render_template, request
from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_homepage = Blueprint('homepage', __name__, template_folder='templates')


@bp_homepage.route('/')
def homepage():
    return render_template('homepage.html')


@bp_homepage.route('/about')
def about():
    return render_template('about.html')



@bp_homepage.route('/create_cocktail', methods=['GET', 'POST'])
def create_cocktail():
    form = IngredientsForm()
    if form.validate_on_submit():
        pass
    return render_template('create_cocktail.html', form=form)

