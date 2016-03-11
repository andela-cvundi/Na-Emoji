Naemoji 💪
=========

Simple API for emojis.
~~~~~~~~~~~~~~~~~~~~~~

|Scrutinizer Code Quality| |Build Status| `[Software
License][ico-license]`_

Naemoji is a simple to use API for emojis.

Usage
-----

First register as a user by sending a POST request to
``https://naemoji-staging.herokuapp.com/auth/register`` and pass in a
username and a password

Login
~~~~~

Some api routes require authentication, so once you have registered, you
can send a ``POST`` request to
``https://emoji-api.herokuapp.com/auth/login`` to receive an
authentication token.

The response for a successful login is:

.. code:: json

    {
        "username":"vundi",
        "token": "5e4aed96bb11d49269e7e7908baee2b4"
    }

Otherwise you will get differnt responses based on what it is you are
not doing right

Get All Emojis
~~~~~~~~~~~~~~

To get all emojis send a ``GET`` request to
``https://naemoji-staging.herokuapp.com/emojis``. The response is a json
with all the emojis from the API.

.. code:: json

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

Get One Emoji
~~~~~~~~~~~~~

If you know the id of an emoji, send a ``GET`` request to
``https://naemoji-staging.herokuapp.com/emoji/{id}`` , with the id of
the emoji you want to retrieve. The response is in JSON.

.. code:: json

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

Add new Emoji
~~~~~~~~~~~~~

This is one of the routes that requires authentication. Send a ``POST``
request to ``https://naemoji-staging.herokuapp.com/emoji`` with the
following object as a parameter,

.. code:: javascript

    {
        name: "cool",
        char: "😎",
        keywords: "['cool ', 'smiley']",
        category: "dope"
    }

and pass in a token field and the value in the header like this
``token = {tokenvalue}``

Update/Patch an Emoji
~~~~~~~~~~~~~~~~~~~~~

These also require authentication. So **make sure** you have passed in
``token = {tokenvalue}`` in the requst header. Make a ``PUT`` or
``PATCH`` request to
``https://naemoji-staging.herokuapp.com/emoji/{id}`` with the correct id
of the emoji you want to update/patch. Include an object in your
request, with the updated details of the emoji. \`\`\`javascript { name:
“cool”, char: “😎”, keywords: “[‘cool’, ‘smiley’]”, cat

.. _[Software License][ico-license]: LICENSE.md

.. |Scrutinizer Code Quality| image:: https://scrutinizer-ci.com/g/andela-cvundi/Na-Emoji/badges/quality-score.png?b=master
   :target: https://scrutinizer-ci.com/g/andela-cvundi/Na-Emoji/?branch=master
.. |Build Status| image:: https://scrutinizer-ci.com/g/andela-cvundi/Na-Emoji/badges/build.png?b=master
   :target: https://scrutinizer-ci.com/g/andela-cvundi/Na-Emoji/build-status/master