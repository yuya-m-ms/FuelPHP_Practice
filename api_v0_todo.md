FORMAT: 0

# REST-like API Root [/]
This is a REST-like API powered by FuelPHP. Returns JSON in default.

## Common Path of version 0 [/api/v0]

### On Fatal Error [GET]
or in DELETE, POST, PUT

+ Response 500 (application/json)
        {"errors":
            {"0": "Fatal Error"
            , "traces": {
                      "1": "string"
                    , "2": "…"
                }
            }
        }

# Group ToDos
Resources related to ToDos in the API.

## ToDo List [GET /api/v0/todo/list/user/{id}]
List all the ToDos of the user.

See below

+ Parameters
    + id: 1 (number) - An unique identifier of the user.


## ToDo List [GET /api/v0/todo/list/me]
List all the ToDos of the current user.

See below


## ToDo List [/api/v0/todo/list]
List all the ToDos of the current user.

### List All ToDos [GET]

+ Response 200 (application/json)

        {"list":
            { 1: {"id":1, "name":"ToDo", "due":null, "status_id":0, "deleted":false "user_id":1}
            , 2: {"…":"…", …}
            , …
            }
        }

+ Response 200 (application/xml)

        <xml>
            <list>
                <item>
                    <id>1</id>
                    <name>ToDo</name>
                    <due></due>
                    <status_id>0</status_id>
                    <deleted>false</deleted>
                    <user_id>1</user_id>
                </item>
                <item>…</item>
                …
            </list>
        </xml>


## ToDo Item [/api/v0/todo/item/{id}]

+ Parameters
    + id: 1 (number) - An unique identifier of the ToDo.

### Get ToDo Item [GET]
Return Gone if the ToDo is deleted.

+ Response 200 (application/json)
    + Body
        {"item":
            {"id":1, "name":"ToDo", "due":null, "status_id":0, "deleted":false, "user_id":1}
        }

+ Response 406


### Delete ToDo Item [DELETE]

+ Response 204


### Add ToDo Item [POST]

+ Response 200 (application/json)
    + Headers
        Location: URI
    + Body
        {"item":
            {"id":1, "name":"ToDo", "due":null, "status_id":0, "deleted":false, "user_id":1}
        }

### Update ToDo Item [PUT]

+ Response 200 (application/json)
    + Headers
        Location: URI
    + Body
        {"item":
            {"id":1, "name":"ToDo", "due":null, "status_id":0, "deleted":false, "user_id":1}
        }
