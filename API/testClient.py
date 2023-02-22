#!/usr/bin/env python
import pika
import sys

connection = pika.BlockingConnection(pika.ConnectionParameters('127.0.0.1', '5672', 'testHost', pika.PlainCredentials('test', 'test')))
channel = connection.channel()

channel.exchange_declare(exchange='testExchange', exchange_type='topic')


message = 'Test from API to Rabbit'
channel.basic_request(exchange='direct_logs', body=message)

print(" [x] Sent %r" % (message))
connection.close()