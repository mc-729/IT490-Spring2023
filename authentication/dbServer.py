import json
import bcrypt
import mysql.connector
import hashlib
import random
from rabbitmqlibPYTHON import RabbitMQClient
from rabbitMQserver import rabbitMQServer

def dbConnection():
    servername = 'localhost'
    uname = 'testuser'
    pw = '12345'
    dbname = 'IT490'
    conn = mysql.connector.connect(user=uname, password=pw, host=servername, database=dbname)

    if conn.is_connected():
        print('Successfully Connected!')
    else:
        print('Failed to connect to MySQL:')

    return conn

def loginAuth(username, password):
    conn = dbConnection()
    cursor = conn.cursor()
    sql = f"SELECT * FROM IT490.Users WHERE Email = '{username}'"
    cursor.execute(sql)
    result = cursor.fetchone()

    if result:
        print('User Found')

        hashedpass = result[4]

        if hashlib.sha256(password.encode('utf-8')).hexdigest() == hashedpass:
            print('Login Successful')
            resp = {
                'login_status': True,
                'session_id': SessionGen(result[0], conn),
                'user_id': result[0],
                'first_name': result[1],
                'last_name': result[2],
                'username': result[3],
                'email': result[4]
            }
            return resp
        else:
            print('Login Failed')
            resp = {
                'login_status': False,
                'session_id': None,
                'user_id': None,
                'first_name': None,
                'last_name': None,
                'username': None,
                'email': None
            }
            return resp
    else:
        print('Login Failed')
        resp = {
            'login_status': False,
            'session_id': None,
            'user_id': None,
            'first_name': None,
            'last_name': None,
            'username': None,
            'email': None
        }
        return resp

def registrationInsert(username, password, email, firstName, lastName):
    conn = dbConnection()
    cursor = conn.cursor()

    sql = f"SELECT * FROM IT490.Users WHERE Email = '{email}'"
    cursor.execute(sql)
    result = cursor.fetchone()

    if result:
        print('Username/Email already exists, please use a different one.')
        resp = {'login_status': False}
        return resp
    else:
        hashedpass = hashlib.sha256(password.encode('utf-8')).hexdigest()
        sql = f"INSERT INTO IT490.Users (Username, F_Name, L_Name, Email, Password) VALUES ('{username}', '{firstName}', '{lastName}', '{email}', '{hashedpass}')"

        if cursor.execute(sql):
            print('New user registered, welcome.')
            resp = {'login_status': True}
            return resp
        else:
            print('Error with query')
            resp = {'login_status': False}
            return resp

def SessionGen(user_ID, conn):
    cursor = conn.cursor()
    conn = dbConnection()
    check = f"SELECT * from IT490.sessions where UID = {user_ID}"
    cursor.execute(check)
    count = cursor.fetchone()

    sessionID = random.randint(1000, 99999999)
    query2 = f"INSERT into IT490.sessions(UID,SessionID)VALUES({user_ID},{sessionID})"
    cursor.execute(query2)
    conn.commit()
    return sessionID

def logout(sessionid):
    conn = dbConnection()
    query = f"DELETE FROM IT490.sessions WHERE SessionID = '{sessionid}'"
    
    if conn.cursor().execute(query):
        conn.commit()
        return True
    else:
        return False

def do_validate(session_id):
    count = 0
    if session_id is not None:
        conn = dbConnection()
        sql = f"SELECT * FROM IT490.sessions WHERE SessionID = '{session_id}'"
        result = conn.cursor().execute(sql)
        row = result.fetchone()
        count = result.rowcount
    print(count)
    if count != 0:
        print('Session is valid')
        return {'session_status': True}
    else:
        print('Session is not valid')
        return {'session_status': False}
def update_profile(sessionid, username, newpassword, oldpassword, email, firstName, lastName):
    conn = dbConnection()
    sql = 'UPDATE Users SET'

    if do_validate(sessionid):
        sql2 = f"SELECT UID FROM IT490.sessions WHERE sessionID = '{sessionid}'"
        result = conn.execute(sql2)
        row = result.fetchone()
        userid = row['UID']

        if newpassword and oldpassword:
            sql2 = f"SELECT Password FROM IT490.Users WHERE User_ID = '{userid}'"
            result2 = conn.execute(sql2)
            row2 = result2.fetchone()
            hashed_pass = row2['Password']

            if bcrypt.checkpw(oldpassword.encode('utf-8'), hashed_pass.encode('utf-8')):
                hashed_password = bcrypt.hashpw(newpassword.encode('utf-8'), bcrypt.gensalt())
                sql += f" Password='{conn.escape(hashed_password.decode('utf-8'))}',"

        if username:
            sql += f" Username='{conn.escape(username)}',"

        if email:
            sql += f" Email='{conn.escape(email)}',"

        if firstName:
            sql += f" f_name='{conn.escape(firstName)}',"

        if lastName:
            sql += f" l_name='{conn.escape(lastName)}',"

        sql = sql.rstrip(',')
        sql += f" WHERE User_ID={userid}"
        result = conn.execute(sql)

        if result:
            return True

    else:
        logout(sessionid)


def fetch_search_results_cached(query):
    try:
        print("Did we make it here?")
        str_query = ",".join(query)
        conn = dbConnection()
        with conn.cursor() as cursor:
            sql = f"SELECT * FROM IT490.Cache WHERE SearchKey = '{str_query}'"
            cursor.execute(sql)
            row = cursor.fetchone()
        count = cursor.rowcount

        if count == 0:
            print("It was not in cache")
            client = RabbitMQClient('RabbitMQConfig.ini', 'APIServer')
            search_results = client.send_request(query)
            store_search_results_in_cache(query, search_results)
            str_query = ",".join(query)
            with conn.cursor() as cursor:
                sql = f"SELECT * FROM IT490.Cache WHERE SearchKey = '{str_query}'"
                cursor.execute(sql)
                row = cursor.fetchone()
            return row['Results']
        else:
            print("It was in cache")
            search_results = row['Results']
            print(type(search_results))
            return search_results
            #print(search_results)
    except Exception as e:
        print(f"Caught exception: {e}")
        return {'API_REQUEST_STATUS': False}
def store_search_results_in_cache(query, search_results):
    obj = json.loads(search_results)
    print(type(obj))
    count = 0
    
    # Convert results to JSON
    json_data = json.dumps(search_results)
    filtered_json = "[" + json_data.filter(str.isalnum) + "]"
    query = ",".join(query)
    #print(json_data)

    # Insert JSON data into database using prepared statement
    conn = dbConnection()
    with conn.cursor() as cursor:
        sql = "INSERT INTO IT490.Cache (SearchKey, Results) VALUES (%s, %s)"
        cursor.execute(sql, (query, filtered_json))
    conn.commit()
    conn.close()

    # Check for errors and return result
    if cursor.rowcount:
        print("It has been added to the cache")
        return True
    else:
        print("Something went wrong in the cache")
        return False
def request_processor(request):
    print('received request')
    print(request)
    if 'type' not in request:
        return {'returnCode': '1', 'message': 'unsupported message type'}
    elif request['type'] == 'Login':
        return loginAuth(request['username'], request['password'])
    elif request['type'] == 'Register':
        return registrationInsert(request['username'], request['password'], request['email'], request['firstName'], request['lastName'])
    elif request['type'] == 'validate_session':
        return do_validate(request['sessionID'])
    elif request['type'] == 'Logout':
        return logout(request['sessionID'])
    elif request['type'] == 'API_CALL':
        return fetch_search_results_cached(request['key'])
    elif request['type'] == 'Update':
        return update_profile(request['sessionID'], request['username'], request['newPW'], request['oldPW'], request['email'], request['firstName'], request['lastName'])
    else:
        return {'returnCode': '1', 'message': 'unsupported message type'}
def my_callback(payload):
    # process the payload here
    return request_processor(payload)


server = rabbitMQServer("testServer")
server.process_requests(my_callback)