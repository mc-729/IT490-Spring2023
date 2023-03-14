#!/usr/bin/env python
import pika
import requests
import json
import os
import api_keys
import ast
class SearchByName:
    @staticmethod
    def get_result(dictionary:{"type":"","operation":"","searchTerm":""}):
        url = "https://the-cocktail-db.p.rapidapi.com/search.php"    
        querystring = {dictionary['operation']:dictionary['searchTerm']}
        print(querystring)
        response = requests.request("GET", url, headers=headers, params=querystring)
        response = response.json()
        return response

class SearchByIngredient:
    @staticmethod
    def get_result(dictionary:{"type":"","operation":"","searchTerm":""}):
        url = "https://the-cocktail-db.p.rapidapi.com/filter.php"
        querystring = {"i":dictionary['searchTerm']}
        print(querystring)
        response = requests.request("GET", url, headers=headers, params=querystring)
        response = response.json()
        return response

class GetCocktailDetailsByID:
    @staticmethod
    def get_result(dictionary:{}):
    # Matt
    # returns full details for cocktail by its ID
    # example query string querystring = {"i":"11007"}

        url = "https://the-cocktail-db.p.rapidapi.com/lookup.php"
        querystring = {}
        response = requests.request("GET", url, headers=headers, params=querystring)
        response = response.json()
        return response

class Random10Cocktails:
    @staticmethod
    def get_result(dictionary:{}):
        url = "https://the-cocktail-db.p.rapidapi.com/randomselection.php"
        response = requests.request("GET", url, headers=headers)
        response = response.json()
        return response

class FilterByCategory:
    @staticmethod
    def get_result(dictionary:{}):
    # Jon
    # returns Drinks of that Type such as Cocktails, Oridinary Drink, etc
    # example query string querystring = {"c":"Cocktail"}

        url = "https://the-cocktail-db.p.rapidapi.com/filter.php"
        querystring = {}
        response = requests.request("GET", url, headers=headers, params=querystring)
        response = response.json()
        return response

class ListIngredients:
    @staticmethod
    def get_result(dictionary:{}):
        url = "www.thecocktaildb.com/api/json/v1/1/list.php?i=list"
        response = requests.request("GET", url, headers=headers)
        response = response.json()
        return response

class SearchIngredientInfo:
    @staticmethod
    def get_result(dictionary:{}):
        url = "https://the-cocktail-db.p.rapidapi.com/search.php"
        querystring = {"i":dictionary['searchTerm']}
        print(querystring)
        response = requests.request("GET", url, headers=headers, params=querystring)
        response = response.json()
        return response

class GoogleEventSearch:
    @staticmethod
    def get_result(dictionary:{"type":"","operation":"","searchTerm":"","location":""}):
        parameter = {"engine":"google_events","q":dictionary['searchTerm'],"location":dictionary['location'],"hl":"en","gl":"us","api_key":"70f09adf5a0cf704be3787eb3243842f1abe44ad14f109e92d18ad82eb9392f2"}
        search = GoogleSearch(parameter)
        response = search.get_dict()
        events_results = response["events_results"]
        response = json.dumps(events_results)
        print(events_results)
        return events_results

class ToJsonFile:
    @staticmethod
    def to_json_file(response,fileName):
        # add response to json file
        os.chdir('json_cache_files/')
        cwd = os.getcwd() + "/"
        fileName = "/" + fileName 
        path = cwd + fileName
        print("Results count: " +str(len(response['drinks'])))
        
        with open(path,"w") as write_file:
            #json.dump(results_count, write_file)
            json.dump(response, write_file, indent=2)  
class APIRoute:
    @staticmethod
    def get_result(dictionary):
     
        try:
           
            match dictionary['type']:
                
                case 'SearchByName':
                    response = json.dumps(SearchByName.get_result(dictionary))
                    return response
                case 'SearchByIngredient':
                    response = json.dumps(SearchByIngredient.get_result(dictionary))
                    return response
                case 'GetCocktailDetailsByID':
                    response = json.dumps(GetCocktailDetailsByID.get_result(dictionary))
                    return response
                case 'Random10Cocktails':
                    response = json.dumps(Random10Cocktails.get_result(dictionary))
                    return response
                case 'FilterByCategory':
                    response = json.dumps(FilterByCategory.get_result(dictionary))
                    return response
                case 'ListIngredients':
                    response = json.dumps(ListIngredients.get_result(dictionary))
                    return response
                case 'SearchIngredientInfo':
                    response = json.dumps(SearchIngredientInfo.get_result(dictionary))
                    return response
                case 'GoogleEventSearch':
                    response = json.dumps(GoogleEventSearch.get_result(dictionary))
                    return response
            return dictionary
        except Exception as err:
            msg = 'No API route found'
            print(f"Unexpected {err=}, {type(err)=}")
            #print("we have an error")
            return msg
                
                




headers = {
	"X-RapidAPI-Key": api_keys.api_key, # please keep the 100/day limit in mind for the Cocktail DB, if you are working on it 
    "X-RapidAPI-Host": "the-cocktail-db.p.rapidapi.com",                    # just input your api key so you can keep track of calls
	"Content-Type": "application/json"                                                                      
}





    



print(" [x] Awaiting RPC requests")

def getServer(servername:str):
    if servername == "testServer":
        return {
            'BROKER_HOST': '127.0.0.1',
            'BROKER_PORT': '5672',
            'USER': 'test',
            'PASSWORD': 'test',
            'VHOST': 'testHost',
            'EXCHANGE': 'testExchange',
            'QUEUE': 'testQueue',
            'EXCHANGE_TYPE': 'topic',
            'AUTO_DELETE': True
        }
    elif servername == 'APIServer':
        return {
            'BROKER_HOST': '127.0.0.1',
            'BROKER_PORT': '5672',
            'USER': 'test',
            'PASSWORD': 'test',
            'VHOST': 'testHost',
            'EXCHANGE': 'testExchange',
            'QUEUE': 'API_QUEUE',
            'EXCHANGE_TYPE': 'topic',
            'AUTO_DELETE': True
        }
    else:
        raise ValueError(f"Invalid server name: {servername}")

connection = pika.BlockingConnection(pika.ConnectionParameters('127.0.0.1', '5672', 'testHost', pika.PlainCredentials('test', 'test')))

channel = connection.channel()

channel.queue_declare(queue='API_QUEUE',auto_delete=True,exclusive=False)
channel.queue_bind(exchange="testExchange",queue="API_QUEUE",routing_key='*.response')

headers = {
	"X-RapidAPI-Key": api_keys.api_key, # please keep the 100/day limit in mind for the Cocktail DB, if you are working on it 
    "X-RapidAPI-Host": "the-cocktail-db.p.rapidapi.com",                    # just input your api key so you can keep track of calls
	"Content-Type": "application/json"                                                                      
}



def on_request(ch, method, props, body):
   
 
    n = json.loads(body)
    response = json.dumps(APIRoute.get_result(n),indent=2)
    ch.basic_publish(exchange='',
                     routing_key='*',
                     properties=pika.BasicProperties(correlation_id = \
                                                         props.correlation_id),
                     body=str(response))
    ch.basic_ack(delivery_tag=method.delivery_tag)

channel.basic_qos(prefetch_count=1)
channel.basic_consume(queue='API_QUEUE', on_message_callback=on_request)

print(" [x] Awaiting RPC requestsss")
channel.start_consuming()
