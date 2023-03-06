import ast
from functools import wraps
from flask import Blueprint, render_template, redirect, url_for, flash, request

from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
from flask import session
from application.bp.authentication.forms import RegisterForm , LoginForm, EditProfileForm


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
            response=str(response).replace('"',"")
            response=str(response)[3:]
            response=str(response)[:-2]
            response=str(response).split(',')
            session['firstName']=response[3]
            session['lastName']=response[4]
            session['sessionID']=response[1]
            session['username']=response[5]
            session['email']=response[6]
        
         
            return redirect('/dashboard')
        else:
            flash('Login unsuccessful. Please check your username and password.', 'danger')

    return render_template('login.html', form=form)

def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if session.get("sessionID") is None:
            flash("Fuck Off!!!", "error")
            return redirect(url_for("authentication.login"))
        return f(*args, **kwargs)
    return decorated_function

@authentication.route('/logout')
@login_required
def logout():
    # Send message to RabbitMQ to log out the user
    client = RabbitMQClient('testServer')
    request_data = {
        'type': 'Logout',
        'sessionID': session.get('sessionID')
    }
    response = client.send_request(request_data)

    if response:
        session.clear() 
       
        return redirect(url_for('authentication.login'))

    return 'Something went wrong.'

        

@authentication.route("/edit_profile", methods=["GET", "POST"])
@login_required
def edit_profile():
    form = EditProfileForm()
    form.username.data=session.get('username')
    form.email.data=session.get('email')
    form.last_name.data=session.get('lastName')
    form.first_name.data=session.get('firstName')

    if form.validate_on_submit():
        # Build the message payload for updating the user's profile information
        payload = {
            'type': 'Update',
            "sessionID": session.get("sessionID"),
            "firstName": form.first_name.data,
            "lastName": form.last_name.data,
            "email": form.email.data,
            "username": form.username.data
            , "newPW":form.new_password.data , "oldPW": form.old_password.data 

        }

        # Send the message to the RabbitMQ server for processing
        client = RabbitMQClient('testServer')
        response = client.send_request(payload)

        if response:
            # Update session data with new values
            session["first_name"] = form.first_name.data
            session["last_name"] = form.last_name.data
            session["email"] = form.email.data
            session["username"] = form.username.data

            flash("Profile information updated successfully", "success")
            return redirect(url_for("authentication.edit_profile"))
        else:
            flash("Failed to update profile information", "error")

    return render_template("edit_profile.html", form=form)
      
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
      

        if response:
         
            return redirect(url_for('authentication.login'))
        else:
            return redirect(url_for('authentication.registration'))
    return render_template('registration.html',form=form)

