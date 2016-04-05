FORMAT: 0

# REST-like API Root [/]
This is a REST-like API powered by FuelPHP. Returns JSON in default.

## Common Path of version 0 [/api/v0]

# Group ToDos
Resources related to ToDos in the API.


## ToDo List [/todo/list/user/{id}]
List all the ToDos of the user.

+ Parameters
    + id: 1 (number) - An unique identifier of the user.

### GET
See below

## ToDo List [/todo/list/me]
List all the ToDos of the current user.

### GET
See below

## ToDo List [/todo/list]

### List All ToDos [GET]

+ Response 200 (application/json)

        {"list":
            { n:
                {"name":name, "due":due, "status_id":status_id, "user_id":user_id}
            , n: {"…":"…", …}
            , …
            }
        }

+ Response 200 (application/xml)

        <xml>
            <list>
                <item>
                    <id>n</id>
                    <name>name</name>
                    <due></due>
                    <status_id>n</status_id>
                    <user_id>n</user_id>
                </item>
                <item>…</item>
                …
            </list>
        </xml>



## ToDo Item [/todo/item/{id}]

+ Parameters
    + id: 1 (number) - An unique identifier of the ToDo.

### Get ToDo Item [GET]
Return Gone if the ToDo is deleted.

+ Response 200 (application/json)
    + Body
        {"item":
            {id:
                {"id":id, "name":name, "due":due, "status_id":status_id, "user_id":user_id}
            }
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
            {id:
                {"id":id, "name":name, "due":due, "status_id":status_id, "user_id":user_id}
            }
        }

### Update ToDo Item [PUT]

+ Response 200 (application/json)
    + Headers
        Location: URI
    + Body
        {"item":
            {id:
                {"id":id, "name":name, "due":due, "status_id":status_id, "user_id":user_id}
            }
        }
