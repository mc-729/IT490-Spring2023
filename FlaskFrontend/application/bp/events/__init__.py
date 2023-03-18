
import ast
import json
from flask import Blueprint, jsonify, render_template, request, session
from application.bp.authentication.forms import SearchForm , IngredientsForm, LikeButton, EventsForm
from application.rabbitMQ.rabbitmqlibPYTHON import RabbitMQClient
bp_events = Blueprint('events', __name__, template_folder='templates')

    # Process the data and return a response    
@bp_events.route('/sendEventData', methods=['GET','POST'])
def sendEventData():
     data = request.get_json()
     print(data)
     UID=session['user_id']
    
     data=json.dumps(data)
     data=json.loads(data)
     eventName=ast.literal_eval(data)["title"]
     eventURL=ast.literal_eval(data)["link"]
     eventImage=ast.literal_eval(data)["image"]
     eventDescription=ast.literal_eval(data)["description"]
     eventDate=ast.literal_eval(data)["date"]["start_date"]
     print(eventDate)
     client = RabbitMQClient('testServer')
     request_dict = {
                'type': 'SaveEvent',
                'name': eventName,
                'date': eventDate,
                'image': eventImage,
                'description': eventDescription,
                'URL': eventURL, 
                'UID': UID            
            }

     response = client.send_request(request_dict)
     if(response):
        response = {"status": "success", "message": "Data received successfully."}
     #   flash("you have liked the recipe for %s"%eventName, "success")
     else:
        response = {"status": "error", "message": "something went wrong"}
    
     return jsonify(response)

    # Return a response indicating the request was successful

@bp_events.route('/events', methods=['GET', 'POST'])
def events():
    data = {}
    start_dates = []
    form = EventsForm()
    like = LikeButton()
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
    return render_template('events.html', form=form, data=data, start_dates=start_dates, like=like)

