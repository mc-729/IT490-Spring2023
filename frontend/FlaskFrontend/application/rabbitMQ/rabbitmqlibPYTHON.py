import json
import pika
import uuid
from configparser import ConfigParser
from config import Config
def getServer(servername:str):
    if servername == "testServer":
        return {
            'BROKER_HOST': '127.0.0.1',
            'BROKER_PORT': '5672',
            'USER': 'test',
            'PASSWORD': 'test',
            'VHOST': 'testHost',
            'EXCHANGE': 'testExchange',
            'QUEUE': 'testQueue2',
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

import pika
import json
import uuid


class RabbitMQClient:
    def __init__(self, servername):
        server = getServer(servername)

        self.broker_host = server['BROKER_HOST']
        self.broker_port = server['BROKER_PORT']
        self.user = server['USER']
        self.password = server['PASSWORD']
        self.vhost = server['VHOST']
        self.exchange = server['EXCHANGE']
        self.queue = server['QUEUE']
        self.exchange_type = 'topic'
        self.auto_delete = server['AUTO_DELETE']
        self.routing_key = '*'
        self.response_queue = {}
        self.callback_queue = None

        self.credentials = pika.PlainCredentials(self.user, self.password)
        self.parameters = pika.ConnectionParameters(
            host=self.broker_host,
            port=self.broker_port,
            virtual_host=self.vhost,
            credentials=self.credentials,
        )
        self.connection = pika.BlockingConnection(self.parameters)
        self.channel = self.connection.channel()

    def process_response(self, channel, method, properties, body):
        uid = properties.correlation_id
        if uid not in self.response_queue:
            print("Unknown uid")
            return
        channel.basic_ack(delivery_tag=method.delivery_tag)
        payload = json.loads(body)
        if not payload:
            payload = "[empty response]"
        self.response_queue[uid] = payload

    def send_request(self, message):
        uid = str(uuid.uuid4())

        json_message = json.dumps(message)
        try:
         

            self.channel.exchange_declare(exchange=self.exchange, exchange_type=self.exchange_type)
            callback_queue_name = self.queue + '_response'
            result = self.channel.queue_declare(queue='',auto_delete=True)
            self.callback_queue = result.method.queue
        
            self.channel.queue_bind(exchange=self.exchange, queue=callback_queue_name,routing_key=self.routing_key + '.response')
         

            self.channel.basic_publish(
                exchange='',
                routing_key=self.queue,
                body=json_message,
                properties=pika.BasicProperties(
                    reply_to=self.callback_queue,
                    correlation_id=uid
                )
            )
            self.response_queue[uid] = 'waiting'
            self.channel.basic_consume(queue=self.callback_queue, on_message_callback=self.process_response, auto_ack=True)

            while self.response_queue[uid] == 'waiting':
                self.connection.process_data_events()

            response = self.response_queue[uid]
            del self.response_queue[uid]
            self.connection.close()
            return response

        except Exception as e:
            print(f'failed to send message to exchange: {e}')
            return None

    def publish(self, message):
        json_message = json.dumps(message)
        try:
            credentials = pika.PlainCredentials(self.user, self.password)
            parameters = pika.ConnectionParameters(
                host=self.broker_host,
                port=self.broker_port,
                virtual_host=self.vhost,
                credentials=credentials,
            )

            connection = pika.BlockingConnection(parameters)
            channel = connection.channel()

            channel.exchange_declare(exchange=self.exchange, exchange_type=self.exchange_type)

            self.conn_queue = channel.queue_declare(queue=self.queue)
            channel.queue_bind(exchange=self.exchange, queue=self.conn_queue.method.queue, routing_key=self.routing_key)

            channel.basic_publish(exchange=self.exchange, routing_key=self.routing_key, body=json_message)
            connection.close()
        except Exception as e:
            print(f'failed to send message to exchange: {e}')
