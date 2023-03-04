from flask import Blueprint, render_template, redirect, url_for, flash


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


@authentication.route('/register', methods=['POST'])
def register():
    if request.method == 'POST':
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

    return render_template('register.html')