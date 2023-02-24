#!/usr/bin/env python
import pika
import requests
import json
import os
import api_keys
import ast
class SearchByName:
    @staticmethod
    def get_result(dictionary:{"type":"","operation":"","ingredient":""}):
        url = "https://the-cocktail-db.p.rapidapi.com/search.php"    
        querystring = {dictionary['operation']:dictionary['ingredient']}
        print(querystring)
        response = requests.request("GET", url, headers=headers, params=querystring)
        response = response.json()
        return response
        

    #make a call to json to file function to cache data


class SearchBySingleIngredient:
    @staticmethod
    def get_result(dictionary:{}):
    # Paul
    # return all cocktails with that include this ingredient ex Vodka -> 102 items that contain Vodka as an ingredient
    # Only returns Drink name, Drink Pic, Drink ID
    # example query string querystring = {"i":"Vodka"}
        url = "https://the-cocktail-db.p.rapidapi.com/filter.php"
        querystring = {}
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
    # Done 
    # returns all details about these 10 random cocktails
    # example query string N/A

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
    # done 
    # returns all ingredients
    # example query string N/A

        url = "www.thecocktaildb.com/api/json/v1/1/list.php?i=list"
        response = requests.request("GET", url, headers=headers)
        response = response.json()
        return response

class SearchIngredientInfo:
    @staticmethod
    def get_result(dictionary:{}):
    # Justin
    # return information about input ingredient ex: Gin -> will return what gin is and how it is made
    # example query string querystring = {"i":"vodka"}

        url = "https://the-cocktail-db.p.rapidapi.com/search.php"
        querystring = {}
        response = requests.request("GET", url, headers=headers, params=querystring)
        response = response.json()
        return response

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
        dictionary=ast.literal_eval(dictionary)
        match dictionary['type']:
            case 'SearchByName':
               response = json.dumps(SearchByName.get_result(dictionary))
               return response
        return dictionary
                


connection = pika.BlockingConnection(pika.ConnectionParameters(host='localhost'))

channel = connection.channel()

channel.queue_declare(queue='rpc_queue')

channel.queue_declare(queue='rpc_queue')
headers = {
	"X-RapidAPI-Key": api_keys.api_key, # please keep the 100/day limit in mind for the Cocktail DB, if you are working on it 
    "X-RapidAPI-Host": "the-cocktail-db.p.rapidapi.com",                    # just input your api key so you can keep track of calls
	"Content-Type": "application/json"                                                                      
}




def on_request(ch, method, props, body):
   
 
    n = json.loads(body)
    response = json.dumps(APIRoute.get_result(n))
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