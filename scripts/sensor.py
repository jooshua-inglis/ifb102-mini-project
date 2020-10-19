#!/usr/bin/python3
import os.path
from datetime import datetime

import adafruit_dht
import board
import sqlite3

SCRIPT_PATH = os.path.dirname(os.path.abspath(__file__))
conn = sqlite3.connect(f'{SCRIPT_PATH}/../db/db.sqlite')
cur = conn.cursor()

DHT_TYPE = adafruit_dht.DHT22
DHT_PIN  = board.D2
dhtDevice = DHT_TYPE(DHT_PIN)

def read_value():
    try: 
        temp = dhtDevice.temperature
        humidity = dhtDevice.humidity

        if temp and humidity: 
            print(f'{temp},{humidity}')
            cur.execute('INSERT INTO ambiance values (datetime("now", "localtime"), ?, ?)', (temp, humidity))
        conn.commit()
        conn.close()
    except RuntimeError as error:
        print(f'[{datetime.now().isoformat()}] -- RuntimeError occured: {error.args[0]}')
        read_value()
    except Exception as error:
        dhtDevice.exit()
        print(f'[{datetime.now().isoformat()}] -- error occured: {error.args[0]}')
        
if __name__ == "__main__":
    read_value()

