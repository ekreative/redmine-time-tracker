redmine_tracker
===============

A Symfony project created on June 27, 2015, 10:33 am.

## Install

    1. clone project
    2. composer install
    3. npm-install
    4. gulp
    5. clear cache for prod environment
    6. start resque  

## Resque

For starting resque run __VVERBOSE=1 QUEUE=redmine_tracker ./bin/resque__

For starting scheduler run __VVERBOSE=1 ./bin/resque-scheduler__

# API documentation

__login via redmine and register device:__ POST /api/login
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

```json
{
  "redmine.username": "yuriy.tarnavskiy",
  "redmine.email": "yuriy.tarnavskiy@ekreative.com",
  "redmine.name": "Yuriy",
  "redmine.surname": "Tarnavskiy",
  "redmine.token": "redmine token",
  "user.settings": {
    "sms": true,
    "push": false,
    "checkFirst": "17:45",
    "checkSecond": "20:00",
    "checkThird": "09:30",
    "phone": 05012345678
  }
}
```

__registration device:__ POST /api/device

*Header:*  apikey: RedmineToken

```json
{
    "device": {
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
    "id": 7,
    "pushPlatform": "ios | ios_sb | android",
    "enabled": true
}
```

status 403.

```json
{
    "message": "Redmine user: bad credentials "
}
```

status 400.

```json
{
    "message": "Wrong request"
}
```

---

__remove device and stop resque job for user:__ DELETE /api/device/{id}

*Header:*  apikey: RedmineToken

*Response:* 

status 200. 

```json
{
    "message": "removed"
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

---

__get settings:__ GET /api/user/settings

*Header:*  apikey: redmineToken

*Response:*
status 200:
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

status 403.

```json
{
    "message": "Sorry, unrecognized username or password"
}
```
