#!/usr/bin/env python
import pika
import sys

connection = pika.BlockingConnection(pika.ConnectionParameters('127.0.0.1', '5672', 'testHost', pika.PlainCredentials('test', 'test')))
channel = connection.channel()

channel.exchange_declare(exchange='testExchange', exchange_type='topic', passive=False,durable=True,internal=False)
# exchange_declare(exchange, exchange_type=<ExchangeType.direct: 'direct'>, passive=False, durable=False, auto_delete=False, internal=False, arguments=None, callback=None)


message = 'Test from API to Rabbit'
channel.basic_request(exchange='direct_logs', body=message)

print(" [x] Sent %r" % (message))
connection.close()