
import json
from flask import Blueprint, jsonify, render_template, request
#from flask_modals import render_template_modal
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton, EventsForm
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_events = Blueprint('events', __name__, template_folder='templates')

@bp_events.route('/events', methods=['GET', 'POST'])
def events():
    data = {}
    start_dates = []
    form = EventsForm()
    if form.validate_on_submit():
        search = request.form['search']
        city = str(request.form['city'])
        state = str(request.form['state'])
        location = city +', '+ state

        if search and location:
            client = RabbitMQClient('testServer')
            try:
                request_dict = {
                    'type': 'API_CALL',
                    'key': {
                        'type': 'GoogleEventSearch',
                        'operation': 's',
                        'searchTerm': search,
                        'location' : location
                    }
                }
                response = client.send_request(request_dict)
                response = json.loads(json.loads(response))[0]
                response = json.loads(response)
                
                for event in response:
                    start_dates.append(event['date']['start_date'])
                    print(start_dates)

                #return jsonify(response)
                data = response
                
            except Exception as e:
                print(str(e))
    else:
        response = []
    if form.validate_on_submit():
        pass
    return render_template('events.html', form=form, data=data, start_dates=start_dates)

