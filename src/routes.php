<?php

use Vundi\NaEmoji\Controllers\EmojiController;
use Vundi\NaEmoji\Models\Emoji;
use Vundi\NaEmoji\Model\User;
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
$app->post('/emoji', function ($request, $response, $args) {
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
