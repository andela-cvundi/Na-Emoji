<?php

use Vundi\NaEmoji\Controllers\EmojiController;
use Vundi\NaEmoji\Models\Emoji;
use Vundi\NaEmoji\Models\User;
use Vundi\Potato\Exceptions\NonExistentID;

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
});

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
});


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
});

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
});


// Register a new user
$app->post('/auth/register', function ($request, $response) {
    $data = $request->getParsedBody();
    $username = $data['username'];
    $password = $data['password'];
    $user = User::findWhere(['username' => $username]);
    if (array_key_exists('id', $user[0])) {
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
