#!/usr/bin/env python
import pika
import cocktail_api.py


connection = pika.BlockingConnection(pika.ConnectionParameters('127.0.0.1', '5672', 'testHost', pika.PlainCredentials('test', 'test')))

channel = connection.channel()

channel.queue_declare(queue='rpc_queue')

def api_call(body):
     return search_by_name(body)


def on_request(ch, method, props, body):
   
    # n = int(body)
    n = body

    print("we recieved" % n)
    response = api_cal(n)

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