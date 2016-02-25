<?php

use Vundi\NaEmoji\Controller\EmojiController;
use Vundi\NaEmoji\Model\Emoji;
use Vundi\NaEmoji\Model\User;

date_default_timezone_set('Africa/Nairobi');
// Routes
// $app->get('/[{name}]', function ($request, $response, $args) {
//     // Sample log message
//     $this->logger->info("Slim-Skeleton '/' route");

//     // Render index view
//     return $this->renderer->render($response, 'index.phtml', $args);
// });

$app->get('/', function ($request, $response) {
    // Render index view
    return $response->write('Hello world');
});


$app->get('/emojis', function ($request, $response) {
    return $response->write('deez nuts');
});
