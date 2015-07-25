redmine_tracker
===============

A Symfony project created on June 27, 2015, 10:33 am.


# API documentation

__login:__ POST /api/login

```json
{
    "user": {
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

User object:


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

User object:


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


## User object example:
```json
{
    "redmine.username": "yuriy.tarnavskiy",
    "redmine.email": "yuriy.tarnavskiy@ekreative.com",
    "redmine.name": "Yuriy",
    "redmine.surname": "Tarnavskiy",
    "redmine.token": " token ",
    "user.settings": {
        "sms": true,
        "push": false,
        "checkFirst": "18:00",
        "checkSecond": "20:10",
        "checkThird": "09:47",
        "phone": "0501234567"
    }
}
```
