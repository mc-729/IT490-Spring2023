#!/usr/bin/env
import asyncio
import json
import math
import pika

async def main():

    print("Opening connection to RabbitMQ...")
    try:
        connection = pika.BlockingConnection(pika.ConnectionParameters('127.0.0.1', '5672', 'testHost', pika.PlainCredentials('test', 'test')))
    except:
        print("Failed to connect to RabbitMQ. Check that the connection parameters are correct.")
        return
    channel = connection.channel()

   # channel.exchange_declare(exchange='testExchange', exchange_type='topic')
    print("Declaring queue variables...")
    channel.queue_declare(queue="testQueue", durable=True, arguments={"x-queue-type:": "quorum"})
   # channel.queue_bind(exchange='testExchange', queue="testQueue")
    channel.start_consuming()
asyncio.run(main())    