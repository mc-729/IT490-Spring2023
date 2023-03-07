from flask import Blueprint, render_template
import pika



bp_homepage = Blueprint('homepage', __name__, template_folder='templates')


@bp_homepage.route('/')
def homepage():
    return render_template('homepage.html')


@bp_homepage.route('/about')
def about():
    return render_template('about.html')


@bp_homepage.route('/drinkwithyoureyes')
def drinkwithyoureyes():
    return render_template('drinkwithyoureyes.html')






