# Naemoji 💪
### Simple API for emojis.

[![Build Status](https://travis-ci.org/andela-cganga/emoji-api.svg?branch=staging)](https://travis-ci.org/andela-cganga/emoji-api)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andela-cganga/emoji-api/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/andela-cganga/emoji-api/?branch=staging)
[![StyleCI](https://styleci.io/repos/49257737/shield)](https://styleci.io/repos/49257737)

Naija Emoji is a simple to use API for emojis.

##Usage
First register as a user by sending a POST request to `https://naemoji-staging.herokuapp.com/auth/register` and pass in a username and a password

### Login
Some api routes require authentication, so once you have registered, you can send a `POST` request to `https://emoji-api.herokuapp.com/auth/login` to receive an authentication token.

The response for a successful login is:
```json
{
    "username":"vundi",
    "token": "5e4aed96bb11d49269e7e7908baee2b4"
}
```
Otherwise you will get differnt responses based on what it is you are not doing right

### Get All Emojis
To get all emojis send a `GET` request to `https://naemoji-staging.herokuapp.com/emojis`. The response is a json with all the emojis from the API.

```json
[
  {
    "id": 1,
    "name": "jason",
    "char": "🤖",
    "keywords": "['machete ', 'fierce',]",
    "category": "Danger",
    "date_created": "2016-01-17 12:24:59",
    "date_modified": "2016-01-17 12:24:59",
    "created_by": "vundi"
  },
  {
    "id": 2,
    "name": "cool",
    "char": "😎",
    "keywords": "['Cool ', 'gangsta']",
    "category": "Fun",
    "date_created": "2016-01-17 12:27:04",
    "date_modified": "2016-01-17 12:27:04",
    "created_by": "vundi"
  }
]
```

### Get One Emoji
If you know the id of an emoji, send a `GET` request to `https://naemoji-staging.herokuapp.com/emoji/{id}` , with the id of the emoji you want to retrieve. The response is in JSON.
```json
{
    "id": 2,
    "name": "cool",
    "char": "😎",
    "keywords": "['Cool ', 'gangsta']",
    "category": "Fun",
    "date_created": "2016-01-17 12:27:04",
    "date_modified": "2016-01-17 12:27:04",
    "created_by": "vundi"
}
```

### Add new Emoji
This is one of the routes that requires authentication. Send a `POST` request to `https://naemoji-staging.herokuapp.com/emoji` with the following object as a parameter,
```javascript
{
    name: "cool",
    char: "😎",
    keywords: "['cool ', 'smiley']",
    category: "dope"
}
```
and pass in a token field and the value in the header like this `token = {tokenvalue}`

### Update/Patch an Emoji
These also require authentication. So **make sure** you have passed in `token = {tokenvalue}` in the requst header. Make a `PUT` or `PATCH` request to `https://naemoji-staging.herokuapp.com/emoji/{id}` with the correct id of the emoji you want to update/patch. Include an object in your request, with the updated details of the emoji.
```javascript
{
    name: "cool",
    char: "😎",
    keywords: "['cool ', 'smiley']",
    category: "woooah"
}
```

### Delete an Emoji
You can delete an emoji from Naija Emoji Api by sending a `DELETE` request to `https://naemoji-staging.herokuapp.com/emoji/{id}` , Make sure you have passed in the token in the requst header like this `token = {tokenvalue}` since delete also requires authorization.

### Logout
You can destroy your token access to EmojiApi by simply making a `GET` request to `https://naemoji-staging.herokuapp.com/auth/logout`. Pass in a token value in the header the same way you would pass when performing operations which require authorization.

## Credits
[Christopher Vundi](https://github.com/andela-cvundi)