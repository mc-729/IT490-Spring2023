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
    
        resp=json.loads(response.decode("utf-8").replace("'",'"'))
     
        # Check if login was successful
        if resp['login_status']==True:
            session['firstName']=resp['first_name']
            session['lastName']=resp['last_name']
            session['sessionID']=resp['session_id']
            session['username']=resp['username']
            session['email']=resp['email']
            session['city']=resp['city']
            session['state']=resp['state']
            session['user_id']=resp['user_id']
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

        

@authentication.route("/edit_profile", methods=[ "GET","POST"])
@login_required
def edit_profile():
    form = EditProfileForm(first_name_placeholder= session["firstName"] 
                           , last_name_placeholder= session["lastName"] 
                            , username_placeholder=  session["username"]
                            , email_placeholder=session["email"]
                             , city_placeholder=session["city"]
                             , state_placeholder=session["state"])
    
    
    if form.validate_on_submit():
        # Build the message payload for updating the user's profile information
        payload = {
            'type': 'Update',
            "sessionID": session.get("sessionID"),
            "firstName": form.first_name.data,
            "lastName": form.last_name.data,
            "email": form.email.data,
            "username": form.username.data
            , "newPW":form.new_password.data , "oldPW": form.old_password.data,
            "city": form.city.data,
            "state": form.state.data 

        }

        # Send the message to the RabbitMQ server for processing
        client = RabbitMQClient('testServer')
        response = client.send_request(payload)

        if response:
           print("the form first name: "+ form.first_name.data+": last name "+form.last_name.data
                 +": the email is  " + form.email.data + ": the username is" + form.username.data
                 + ":the city is " +form.city.data + ": the state is" + form.state.data)
            # Update session data with new values
           if form.first_name.data != "":session["firstName"] = form.first_name.data
           if form.last_name.data != "": session["lastName"] = form.last_name.data
           if form.email.data != "": session["email"] = form.email.data
           if form.username.data != "": session["username"] = form.username.data
           if form.city.data != "": session["city"] = form.city.data
           if form.state.data != "": session["state"] = form.state.data

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
        city = request.form['city']
        state = request.form['state']

        client = RabbitMQClient('testServer')
        request_data = {
            'type': 'Register',
            'username': uname,
            'password': password,
            'email': email,
            'firstName': first_name,
            'lastName': last_name,
            'city': city,
            'state': state
        }
        response = client.send_request(request_data)
      

        if response:
         
            return redirect(url_for('authentication.login'))
        else:
            return redirect(url_for('authentication.registration'))
    return render_template('registration.html',form=form)

@authentication.route('/myliquorcabinet')
def myliquorcabinet():

    return render_template('myliquorcabinet.html')