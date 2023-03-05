import ast
from flask import Blueprint, render_template, redirect, url_for, flash, request

from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from flask import session
from application.bp.authentication.forms import RegisterForm , LoginForm
from application.HelpFunctions.helperFunctions import User , login_required

import json

authentication = Blueprint('authentication', __name__, template_folder='templates')


@authentication.route('/users')
def users():

    return render_template('users.html')


@authentication.route('/dashboard')
def dashboard():

    return render_template('dashboard.html')


@authentication.route('/users/<user_id>')
def user_by_id(user_id):

    return render_template('user.html')

@authentication.route('/login', methods=['GET', 'POST'])
def login():
    form = LoginForm()
    if form.validate_on_submit():
        email = request.form['email']
        password = request.form['password']

        # Send request to RabbitMQ server
        client = RabbitMQClient('testServer')
        request_data = {
            'type': 'Login',
            'username':email,
            'password': password
        }
        response = client.send_request(request_data)

        # Check if login was successful
        if response:
            response=eval(str(response))
            print(response[0])
            session['user_id']=response[1]
            print(type(response))
            print(response)
            user = User(response[1],response[5],response[6],response[3],response[4])
            print(user.get_username())
            return redirect('/dashboard')
        else:
            flash('Login unsuccessful. Please check your username and password.', 'danger')

    return render_template('login.html', form=form)

"""
@authentication.route('/edit-profile', methods=['GET', 'POST'])
@login_required
def edit_profile():
    form = EditProfileForm()

    if form.validate_on_submit():
        current_user.username = form.username.data
        current_user.email = form.email.data
        current_user.first_name = form.first_name.data
        current_user.last_name = form.last_name.data

        # create RabbitMQ client
        client = RabbitMQClient('testServer')

        # create message data
        message_data = {
            'type': 'UpdateProfile',
            'user_id': current_user.session_id,
            'username': current_user.username,
            'email': current_user.email,
            'first_name': current_user.first_name,
            'last_name': current_user.last_name
        }

        # send message to RabbitMQ server
        response = client.send_request(message_data)

        if response:
            flash('Your profile has been updated.')
        else:
            flash('Something went wrong.')

        return redirect(url_for('authentication.profile'))

    elif request.method == 'GET':
        form.username.data = current_user.username
        form.email.data = current_user.email
        form.first_name.data = current_user.first_name
        form.last_name.data = current_user.last_name

    return render_template('edit-profile.html', form=form)
"""
@authentication.route('/registration', methods=['GET', 'POST'])
def registration():
    form = RegisterForm()
    if form.validate_on_submit():
        uname = request.form['username']
        email = request.form['email']
        password = request.form['password']
        first_name = request.form['fname']
        last_name = request.form['lname']

        client = RabbitMQClient('testServer')
        request_data = {
            'type': 'Register',
            'username': uname,
            'password': password,
            'email': email,
            'firstName': first_name,
            'lastName': last_name
        }
        response = client.send_request(request_data)
        response=json.dumps(response)

        if response:
            return "the return data is "+response+ "the return data type is "+type(response)
            ##return redirect(url_for('authentication.login'))
        else:
            return redirect(url_for('authentication.registration'))
    return render_template('registration.html',form=form)