redmine_tracker
===============

A Symfony project created on June 27, 2015, 10:33 am.

## Configuration

For starting resque run __VVERBOSE=1 QUEUE=redmine_tracker ./bin/resque__

For starting scheduler run __VVERBOSE=1 ./bin/resque-scheduler__

# API documentation

__registration device:__ POST /api/device/registration

```json
{
    "device": {
        "username": "redmine username",
        "password": "redmine password",
        "pushPlatform": "android|ios|ios_sb",
        "pushToken": "push token",
        "deviceId": "device ID"
    }
}
```
*Response:* 

status 200. 

```json
{
    "message" => "device added"
}
```

status 403.

```json
{
    "message": "Sorry, unrecognized username or password"
}
```

---

__unregistration device:__ POST /api/device/remove

```json
{
    "device": {
        "pushToken": "push token",
        "deviceId": "device ID"
    }
}
```
*Response:* 

status 200. 

```json
{
    "message" => "removed"
}
```

status 400.

```json
{
    "message": "Something wrong"
}
```

status 403.

```json
{
    "message": "Sorry, unrecognized username or password"
}
```

---

__update settings:__ POST /api/user/settings

*Header:* apikey: redmineToken

```json
{
    "settings": {
        "sms": true,
        "push": false,
        "checkFirst": "18:00",
        "checkSecond": "20:10",
        "checkThird": "09:47",
        "phone": "05046412345"
    }
}
```
*Response:* 

status 200. 

```json
{
    "sms": true,
    "push": false,
    "checkFirst": "18:00",
    "checkSecond": "20:10",
    "checkThird": "09:47",
    "phone": "05046412345"
}
```

status 400.

```json
{
    "message": "Something wrong"
}
```
status 403. (error validation)

```json
{
    "error": {
        "code": 400,
        "message": "Invalid data",
        "info": {
            "field": "settings",
            "children": [
                {
                    "field": "phone",
                    "errors": [
                        {
                            "message": "Допустимі тільки цифри",
                            "parameters": {
                                "{{ value }}": "\"0504641d2345\""
                            }
                        }
                    ]
                }
            ]
        }
    }
}
```
