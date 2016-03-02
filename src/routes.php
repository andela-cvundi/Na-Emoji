<?php

use Vundi\NaEmoji\Controllers\EmojiController;
use Vundi\NaEmoji\Models\Emoji;
use Vundi\NaEmoji\Models\User;
use Vundi\Potato\Exceptions\NonExistentID;

date_default_timezone_set('Africa/Nairobi');




/*
 * Authorization Middleware
 * Authenticates the requests by checking the user's token
 */
$authMiddleWare = function ($request, $response, $next) {
    $headers = $request->getHeaders();

    if (isset($headers['HTTP_TOKEN'][0])) {
        $token = $headers['HTTP_TOKEN'][0];
        $date = new DateTime();
        $date = $date->format('Y-m-d H:i:s');
        $user = User::findWhere(['token' => $token]);

        $loggedin = [];

        if (! empty($user)) {
            if ($user[0]['token_expire'] > $date) {
                $loggedin = $user[0];
            }

            if (array_key_exists('username', $loggedin)) {
                $request = $request->withAttribute('username', $loggedin['username']);
                $response = $next($request, $response);
            } else {
                $response = $response->withHeader('Content-type', 'application/json');
                $response = $response->withStatus(401);
                $message = [
                    'message' => 'Your token has either expired or invalid. Please login to get the correct token'
                ];
                $json = json_encode($message);
                $response->write($json);
            }
        } else {
            $response = $response->withStatus(401);
            $message = [
                'message' => 'Your is invalid. Please login to get the correct token'
            ];
            $response = $response->withHeader('Content-type', 'application/json');
            $json = json_encode($message);
            $response->write($json);
        }


    } else {
        $response = $response->withStatus(401);
        $response = $response->withHeader('Content-type', 'application/json');
        $message = [
            'message' => 'Please provide an authentication token',
        ];
        $json = json_encode($message);
        $response->write($json);
    }

    return $response;
};


//Get landing page
$app->get('/', function ($request, $response) {
    // Render index view
    return $response->write('Hello world');
});
//Get all emojis
$app->get('/emojis', function ($request, $response) {
    $emojis = Emojicontroller::All();
    $response->getHeader("Content-Type", "application/json");
    echo json_encode($emojis);
});

//Get a specific record
$app->get('/emoji/{id}', function ($request, $response, $args) {
    $emoji = Emojicontroller::find($args['id']);
    if (is_null($emoji)) {
        $response = $response->withStatus(400);
    } else {
        $response = $response->withStatus(200);
    }

    $response = $response->withHeader('Content-type', 'application/json');
    $response->write(json_encode($emoji));

    return $response;
});

// Create a new Emoji.
$app->post('/emoji', function ($request, $response) {
    $data = $request->getParsedBody();

    if (isset($data['name']) && isset($data['char']) && isset($data['category']) && isset($data['keywords'])) {
        $data['username'] = $request->getAttribute('username');
        $emoji = EmojiController::newEmoji($data);
        $response = $response->withStatus(201);
        $message = [
            'success' => true,
            'message' => 'Emoji successfully created',
        ];
    } else {
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
    $id = (int)$args['id'];

    if (isset($data['name']) && isset($data['char']) && isset($data['category']) && isset($data['keywords'])) {
        $emoji = EmojiController::updateEmoji($id, $data);
        if ($emoji['success']) {
            $response = $response->withStatus(201);
            $message = [
                'success' => true,
                'message' => 'Emoji successfully updated',
            ];
        } else {
            $response = $response->withStatus(201);
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
    $data = $request->getParsedBody();
    $id = (int)$args['id'];

    try {
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
                'message' => 'Emoji not partially updated',
            ];

            $response = $response->withStatus(304);
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

$app->delete('/emoji/{id}', function ($request, $response, $args) {

    try {
        $id = (int)$args['id'];
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
    $username = $data['username'];
    $password = $data['password'];
    $user = User::findWhere(['username' => $username]);
    if (isset($user[0]['id'])) {
        $message = [
                'success' => false,
                'message' => 'Username already taken, try a different username',
            ];
        $response = $response->withStatus(400);
    } else {
        $user = new User();
        $user->username = $username;
        $user->password = sha1($password);

        try {
            $user->save();
            $message = [
                'success' => true,
                'message' => 'Account successfully created',
            ];
            $response = $response->withStatus(201);
        } catch (\Exception $e) {
            $message = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            $response = $response->withStatus(500);
        }
    }
    $response = $response->withHeader('Content-type', 'application/json');
    $json = json_encode($message);
    $response->write($json);
    return $response;
});

$app->post('/auth/login', function ($request, $response) {
    $data = $request->getParsedBody();
    $username = $data['username'];
    $password = $data['password'];
    $attReturn = [];
    $loginuser = User::findWhere(['username' => $username]);

    if (array_key_exists('id', $loginuser[0])) {
        if (sha1($password) == $loginuser[0]['password']) {
            $token = bin2hex(openssl_random_pseudo_bytes(16));

            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
            try {
                $user = User::find((int)$loginuser[0]['id']);

                $user->token = $token;
                $user->token_expire = $tokenExpiration;
                $user->update();

                $response = $response->withStatus(200);
                $attReturn = [
                    'user'  => $username,
                    'token' => $token,
                ];
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            $response = $response->withStatus(401);
            $attReturn = [
                'message' => 'Not authenticated. Please make sure you have registered',
            ];
        }
    } else {
        $response = $response->withStatus(401);
        $attReturn = [
            'message' => 'Not authenticated. Please make sure you have registered',
        ];
    }
    $response = $response->withHeader('Content-type', 'application/json');
    $json = json_encode($attReturn);
    $response->write($json);
    return $response;
});

$app->post('/auth/logout', function ($request, $response) {

    try {
        $authuser = User::findWhere(['token' => $request->getHeader('HTTP_TOKEN')[0]]);
        $authuserid = (int)$authuser[0]['id'];

        $user = User::find($authuserid);
        $user->token = '';
        $user->token_expire = '';
        $user->update();
        $response = $response->withStatus(200);

        $message = [
            'message' => 'User logged out successfully',
        ];
    } catch (\Exception $e) {
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
