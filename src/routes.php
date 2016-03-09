<?php

use Vundi\NaEmoji\Controllers\EmojiController;
use Vundi\NaEmoji\Models\Emoji;
use Vundi\NaEmoji\Models\User;
use Vundi\Potato\Exceptions\NonExistentID;
use Vundi\NaEmoji\findWhere;

date_default_timezone_set('Africa/Nairobi');

//Get landing page
$app->get('/', function ($request, $response) {
    // Render index view
    return $response->write('Hello world');
});
//Get all emojis
$app->get('/emojis', function ($request, $response) {
    $emojis = Emojicontroller::All();
    $response->getHeader("Content-Type", "application/json");

    return $response->write(json_encode($emojis));
});

//Get a specific record
$app->get('/emoji/{id}', function ($request, $response, $args) {
    $emoji = Emojicontroller::find($args['id']);
    if (is_null($emoji)) {
        $response = $response->withStatus(400);
    } else {
        $response = $response->withStatus(200);
    }
    //In the response header set the content type to json
    $response = $response->withHeader('Content-type', 'application/json');
    $response->write(json_encode($emoji));

    return $response;
});

// Create a new Emoji.
$app->post('/emoji', function ($request, $response) {
    $data = $request->getParsedBody();

    //Check if all emoji fields have been passed and values assigned
    if (isset($data['name']) && isset($data['char']) && isset($data['category']) && isset($data['keywords'])) {
        //set the username to the username of the person sending the request
        $data['username'] = $request->getAttribute('username');
        $emoji = EmojiController::newEmoji($data);

        //set status code to 201 meaning successfully created
        $response = $response->withStatus(201);
        $message = [
            'success' => true,
            'message' => 'Emoji successfully created',
        ];
    } else {
        //status code to 400 bad request
        $response = $response->withStatus(400);
        $message = [
            'success' => 'Not successful',
            'message' => 'Emoji was not created. Make  sure you pass all the fields',
        ];
    }

    $response = $response->withHeader('Content-type', 'application/json');
    $response->write(json_encode($message));

    return $response;
})->add($authMiddleWare);

// Update an Emoji.
$app->put('/emoji/{id}', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    //cast string to number
    $id = (int)$args['id'];

    //check if all fields have been set since put takes in all the fields
    if (isset($data['name']) && isset($data['char']) && isset($data['category']) && isset($data['keywords'])) {
        $emoji = EmojiController::updateEmoji($id, $data);

        //for a successful update
        if ($emoji['success']) {
            $response = $response->withStatus(201);
            $message = [
                'success' => true,
                'message' => 'Emoji successfully updated',
            ];
        } else {
            $response = $response->withStatus(400);
            $message = [
                'message' => $emoji['message']
            ];
        }
    } else {
        $response = $response->withStatus(400);
        $message = [
            'success' => 'Not successful',
            'message' => 'Emoji was not updated. Make  sure you pass all the fields',
        ];
    }

    $response = $response->withHeader('Content-type', 'application/json');
    $response->write(json_encode($message));

    return $response;
})->add($authMiddleWare);


// Partially update an emoji with ID.
$app->patch('/emoji/{id}', function ($request, $response, $args) {

    try {
        $data = $request->getParsedBody();
        /**
         * Assert key value pairs have been set in the body of the request
         * If not throw na exception
         */
        if (empty($data)) {
            throw new Exception("Nothing to patch here, provide a key value pair to update", 1);
        }
    } catch (Exception $e) {
        $response = $response->withStatus(304);
        return $response;
    }

    try {
        $id = (int)$args['id'];
        $emoji = Emoji::find($id);
        foreach ($request->getParsedBody() as $key => $value) {
            $emoji->{$key} = $value;
        }
        $patch = $emoji->update();

        if ($patch) {
            $message = [
                'success' => true,
                'message' => 'Emoji updated partially',
            ];
            $response = $response->withStatus(201);
        } else {
            $message = [
                'success' => false,
                'message' => 'Emoji not partially updated, Make sure you are passing the correct column names',
            ];
        }

        $response = $response->withHeader('Content-type', 'application/json');
        $response->write(json_encode($message));

        return $response;
    } catch (NonExistentID $e) {
        $message = [
            'success' => false,
            'message' => $e->getMessage()
        ];

        return json_encode($message);
    }
})->add($authMiddleWare);

/**
 * Delete request takes in an id as the parameter
 */
$app->delete('/emoji/{id}', function ($request, $response, $args) {

    try {
        //cast the id parameter into an integer
        $id = (int)$args['id'];

        //Call remove method from the Emoji class
        Emoji::remove($id);
        $response = $response->withStatus(200);
        $message = [
            'success' => true,
            "message" => "Emoji deleted succesfully."
        ];
    } catch (NonExistentID $e) {
        $message = [
            'message' => $e->getMessage()
        ];
    }

    $response = $response->withHeader('Content-type', 'application/json');
    return $response->write(json_encode($message));
})->add($authMiddleWare);


// Register a new user
$app->post('/auth/register', function ($request, $response) {
    $data = $request->getParsedBody();

    /**
     * Make sure username and password are passed in the request body
     * when creating an account
     */
    if (isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        $query = "SELECT * FROM users WHERE username = '$username'";
        $find = new findWhere();
        $user = $find->findResults($query);

        //check if someone with that username already exists in the database
        if (isset($user['id'])) {
            $message = [
                'success' => false,
                'message' => 'Username already taken, try a different username'
            ];
            $response = $response->withStatus(400);
        } else {
            $user = new User();
            /**
             * Set username to the value passed in the body when making the
             * request and hash the password
             */
            $user->username = $username;
            $user->password = sha1($password);

            try {
                $user->save();
                $message = [
                'success' => true,
                'message' => 'Account successfully created'
                ];
                $response = $response->withStatus(201);
            } catch (Exception $e) {
                $message = [
                'success' => false,
                'message' => $e->getMessage(),
                ];
                $response = $response->withStatus(500);
            }
        }
    } else {
        $message = [
            'success' => false,
            'message' => 'Pass username and password'
        ];
        $response = $response->withStatus(400);
    }

    $response = $response->withHeader('Content-type', 'application/json');
    $json = json_encode($message);
    $response->write($json);

    return $response;
});

/**
 * Make a post call to teh login route
 */
$app->post('/auth/login', function ($request, $response) {
    $data = $request->getParsedBody();

    //Username and password must be provided for one to login
    if (isset($data['username']) && isset($data['password'])) {
        /**
         * Get the username and password passed in the body during the post
         * and pass them to variables
         */
        $username = $data['username'];
        $password = $data['password'];
        $attReturn = [];

        $query = "SELECT * FROM users WHERE username = '$username'";
        /**
         * Calling a custom method which looks into the database with the supplied query
         * and returns an array
         */
        $find = new findWhere();
        $loginuser = $find->findResults($query);

        // Check if there are any matches
        if (array_key_exists('id', $loginuser)) {
            /**
             * compare passwords see if they match i.e. what is passed in the body vs
             * what exists in the database
             */
            if (sha1($password) == $loginuser['password']) {
                //generate a token
                $token = bin2hex(openssl_random_pseudo_bytes(16));
                //set the token expiration to current time stamp then add 24 hours
                $tokenExpiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
                try {
                    /**
                     * Set token values and token_expiry value to the logged in user
                     */
                    $user = User::find($loginuser['id']);
                    $user->token = $token;
                    $user->token_expire = $tokenExpiration;
                    $user->update();

                    $response = $response->withStatus(200);
                    $attReturn = [
                        'user'  => $username,
                        'token' => $token
                    ];
                } catch (Exception $e) {
                    return $e->getMessage();
                }
            } else {
                $response = $response->withStatus(401);
                $attReturn = [
                'message' => 'Wrong password. Make sure you type in the password correct'
                ];
            }
        } else {
            $response = $response->withStatus(401);
            $attReturn = [
            'message' => 'Not authenticated. Please make sure you have registered'
            ];
        }
    } else {
        $attReturn = [
            'success' => false,
            'message' => 'Pass username and password'
        ];
        $response = $response->withStatus(400);
    }

    $response = $response->withHeader('Content-type', 'application/json');
    $json = json_encode($attReturn);
    $response->write($json);
    return $response;
});

/**
 * Make a GET request to the logout route
 */
$app->get('/auth/logout', function ($request, $response) {

    try {
        //see if token has been passed in the header
        $token = $request->getHeader('HTTP_TOKEN')[0];

        /**
         * Call the findWhere method and pass in the query to
         * get the user id of the logged in user
         */
        $query = "SELECT * FROM users WHERE token = '$token'";
        $find = new findWhere();
        $authuser = $find->findResults($query);
        $authuserid = $authuser['id'];

        /**
         * Set the token and token_exoiry fields for the logged in user to null
         */
        $user = User::find($authuserid);
        $user->token = '';
        $user->token_expire = null;
        $user->update();
        $response = $response->withStatus(200);

        $message = [
            'message' => 'User logged out successfully',
        ];
    } catch (Exception $e) {
        $message = [
            'message' => $e->getMessage()
        ];
        $response = $response->withStatus(400);
    }

    $json = json_encode($message);
    $response->write($json);

    return $response;
})->add($authMiddleWare);
;
