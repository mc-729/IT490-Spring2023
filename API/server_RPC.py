#!/usr/bin/env python
import pika
import requests
import json
import os
import api_keys


connection = pika.BlockingConnection(pika.ConnectionParameters(host='localhost'))

channel = connection.channel()

channel.queue_declare(queue='rpc_queue')

channel.queue_declare(queue='rpc_queue')
headers = {
	"X-RapidAPI-Key": api_keys.api_key, # please keep the 100/day limit in mind for the Cocktail DB, if you are working on it 
    "X-RapidAPI-Host": "the-cocktail-db.p.rapidapi.com",                    # just input your api key so you can keep track of calls
	"Content-Type": "application/json"                                                                      
}
def search_by_name(dictionary):
    # returns all cocktails with Ingredient in the name ex Vodka -> Vodka fizz, Vodka Martini
    url = "https://the-cocktail-db.p.rapidapi.com/search.php"
    
    querystring = {dictionary['operation']:dictionary['ingredient']}
    print(querystring)
    response = requests.request("GET", url, headers=headers, params=querystring)

    #make a call to json to file function to cache data


    # turns response to json and prints it nicely
    response = response.json()
    return response
    print(json.dumps(response, indent=2))
    #print(json.dumps(response, indent=2))
def api_call(body):
     return search_by_name(body)


def on_request(ch, method, props, body):
   
    # n = int(body)
   # n = json.loads(body)
    testDict = {'operation': 's','ingredient':'vodka' }

   
    print("we recieved" % testDict)
    response = json.dumps(search_by_name(testDict))
    print(json.dumps(search_by_name(testDict), indent=2))
    ch.basic_publish(exchange='',
                     routing_key=props.reply_to,
                     properties=pika.BasicProperties(correlation_id = \
                                                         props.correlation_id),
                     body=str(response))
    ch.basic_ack(delivery_tag=method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(queue='rpc_queue', on_message_callback=on_request)

print(" [x] Awaiting RPC requests")
channel.start_consuming()