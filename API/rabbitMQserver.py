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
            'EXCHANGE': 'apiExchange',
            'QUEUE': 'API_QUEUE',
            'EXCHANGE_TYPE': 'topic',
            'AUTO_DELETE': True
        }
    else:
        raise ValueError(f"Invalid server name: {servername}")


import asyncio
import aio_pika

import aio_pika
import asyncio
import json


class RabbitMQServer:
    def __init__(self, servername):
        self.server = getServer(servername)
        self.broker_host = self.server['BROKER_HOST']
        self.broker_port = self.server['BROKER_PORT']
        self.user = self.server['USER']
        self.password = self.server['PASSWORD']
        self.vhost = self.server['VHOST']
        self.exchange = self.server['EXCHANGE']
        self.queue = self.server['QUEUE']
        self.exchange_type = self.server['EXCHANGE_TYPE']
        self.auto_delete = self.server['AUTO_DELETE']
        self.routing_key = '*'

        self.connection = None
        self.channel = None
        self.consumer_tag = None
        self.conn_queue = None
        self.response_queue = {}

    async def setup(self):
        self.connection = await aio_pika.connect_robust(
            host=self.broker_host,
            port=self.broker_port,
            login=self.user,
            password=self.password,
            virtualhost=self.vhost,
        )

        self.channel = await self.connection.channel()

        await self.channel.set_qos(prefetch_count=10)

        exchange = await self.channel.declare_exchange(
            self.exchange,
            type=self.exchange_type,
            durable=True,
        )

        self.conn_queue = await self.channel.declare_queue(
            self.queue,
            auto_delete=self.auto_delete,
        )

        await self.conn_queue.consume(
            self.handle_request,
            exclusive=False,
        )

    async def shutdown(self):
        if self.consumer_tag:
            await self.channel.cancel(self.consumer_tag)
        if self.channel:
            await self.channel.close()
        if self.connection:
            await self.connection.close()

    async def handle_request(self, message: aio_pika.IncomingMessage):
        async with message.process():
            json_message = message.body.decode('utf-8')
            message_data = json.loads(json_message)
            response = await self.handle_request_async(message_data)
            exchange = await self.channel.get_exchange(self.exchange)
            routing_key = message.reply_to
            correlation_id = message.correlation_id
            response_json = json.dumps(response)
            await exchange.publish(
                aio_pika.Message(
                    body=response_json.encode('utf-8'),
                    content_type='application/json',
                    correlation_id=correlation_id
                ),
                routing_key=routing_key
            )

    async def handle_request_async(self, request):
        print("Received request: ", request)
        try:
            response = APIRoute.get_result(request)
            return response
        except Exception as e:
            print("Error processing request: ", str(e))
            return {"error": str(e)}


        return response

async def main():
    print("we in hurr")
    server = RabbitMQServer('APIServer')
    await server.setup()
    try:
        await asyncio.Future()  # Run forever
        print("async rabbitmq")
    except asyncio.CancelledError:
        pass
    finally:
        await server.shutdown()

if __name__ == '__main__':
    asyncio.run(main())