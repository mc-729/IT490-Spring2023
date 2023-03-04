import json
import pika
from getHost import get_host_info
class RabbitMQServer:
    def __init__(self, machine, server='rabbitMQ'):
        self.machine = get_host_info([machine])
        self.broker_host = self.machine[server]['BROKER_HOST']
        self.broker_port = self.machine[server]['BROKER_PORT']
        self.user = self.machine[server]['USER']
        self.password = self.machine[server]['PASSWORD']
        self.vhost = self.machine[server]['VHOST']
        self.exchange = self.machine[server]['EXCHANGE']
        self.queue = self.machine[server]['QUEUE']
        if 'EXCHANGE_TYPE' in self.machine[server]:
            self.exchange_type = self.machine[server]['EXCHANGE_TYPE']
        else:
            self.exchange_type = 'topic'
        if 'AUTO_DELETE' in self.machine[server]:
            self.auto_delete = self.machine[server]['AUTO_DELETE']
        else:
            self.auto_delete = False
        self.routing_key = '*'
        self.conn_queue = None
        self.callback = None

    def process_message(self, ch, method, properties, body):
        if method.routing_key != '*':
            return
        ch.basic_ack(delivery_tag=method.delivery_tag)
        try:
            if properties.reply_to:
                payload = json.loads(body)
                response = None
                if self.callback is not None:
                    response = self.callback(payload)
                conn = pika.BlockingConnection(pika.ConnectionParameters(
                    host=self.broker_host, port=self.broker_port, virtual_host=self.vhost,
                    credentials=pika.PlainCredentials(self.user, self.password)))
                channel = conn.channel()
                exchange = channel.exchange_declare(
                    exchange=self.exchange, exchange_type=self.exchange_type, auto_delete=self.auto_delete)
                conn_queue = channel.queue_declare(queue=properties.reply_to, auto_delete=True)
                channel.queue_bind(queue=properties.reply_to, exchange=self.exchange, routing_key=self.routing_key + '.response')
                channel.basic_publish(
                    exchange=self.exchange, routing_key=self.routing_key + '.response',
                    properties=pika.BasicProperties(correlation_id=properties.correlation_id),
                    body=json.dumps(response))
                conn.close()
                return
        except Exception as e:
            print(f"error: rabbitMQServer: process_message: exception caught: {e}")
        payload = json.loads(body)
        if self.callback is not None:
            self.callback(payload)
        print("processed one-way message")

    def process_requests(self, callback):
        try:
            self.callback = callback
            conn = pika.BlockingConnection(pika.ConnectionParameters(
                host=self.broker_host, port=self.broker_port, virtual_host=self.vhost,
                credentials=pika.PlainCredentials(self.user, self.password)))
            channel = conn.channel()
            exchange = channel.exchange_declare(
                exchange=self.exchange, exchange_type=self.exchange_type, auto_delete=self.auto_delete)
            self.conn_queue = channel.queue_declare(queue=self.queue, auto_delete=True)
            channel.queue_bind(queue=self.queue, exchange=self.exchange, routing_key=self.routing_key)
            channel.basic_consume(queue=self.queue, on_message_callback=self.process_message)
            channel.start_consuming()
        except Exception as e:
            print(f"Failed to start request processor: {e}")

class RabbitMQClient:
    def __init__(self, machine, server='rabbitMQ'):
        self.machine = get_host_info([machine])
        self.broker_host = self.machine[server]['BROKER_HOST']
        self.broker_port = self.machine[server]['BROKER_PORT']
        self.user = self.machine[server]['USER']
    