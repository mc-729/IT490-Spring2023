from flask import Blueprint, render_template, redirect, url_for, flash, request

from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQServer, RabbitMQClient

from application.bp.authentication.forms import RegisterForm

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


@authentication.route('/registration', methods=['GET', 'POST'])
def registration():
    form = RegisterForm()
    if form.validate_on_submit():
        uname = request.form['username']
        email = request.form['email']
        password = request.form['password']
        first_name = request.form['fname']
        last_name = request.form['lname']

        client = RabbitMQClient('RabbitMQConfig.ini', 'testServer')
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
            return redirect('/loginForm')
        else:
            return 'Something went wrong.'

    return render_template('registration.html',form=form)