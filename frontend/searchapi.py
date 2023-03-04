import json
import sys
from rabbitmq_client import rabbitMQClient
from safer_echo import safer_echo
from nav import nav

from flask import Flask, render_template, request

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/apt_search', methods=['POST'])
def apt_search():
    type = request.form['ans']
    searchByName = request.form['searchValue']

    if (type and searchByName):
        client = rabbitMQClient("RabbitMQConfig.ini", "APIServer")
        request_dict = {"type": type, "operation": "s", "searchTerm": searchByName}
        request = json.dumps(request_dict)
        response = client.send_request(request)
        obj = json.loads(response)
    else:
        obj = {}

    count = 0

    return render_template('apt_search.html', type=type, searchByName=searchByName, obj=obj, count=count)


if __name__ == '__main__':
    app.run(debug=True)