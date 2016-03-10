<?php

use Vundi\NaEmoji\Models\User;
use Vundi\NaEmoji\FindWhere;

date_default_timezone_set('Africa/Nairobi');
/*
 * Authorization Middleware
 * Will be used in requests which require middleware
 */
$authMiddleWare = function ($request, $response, $next) {
    $headers = $request->getHeaders();

    //see if token has been provided
    if (isset($headers['HTTP_TOKEN'][0])) {
        $token = $headers['HTTP_TOKEN'][0];
        $date = new DateTime();
        //save the date as a timestamp
        $date = $date->format('Y-m-d H:i:s');

        $query = "SELECT * FROM users WHERE token = '$token'";
        $find = new findWhere();
        $user = $find->findResults($query);

        $loggedin = [];

        if (! empty($user)) {
            /**
             * If a record has been found with that record check to see if
             * teh token has expired
             */
            if ($user['token_expire'] > $date) {
                $loggedin = $user;
            }

            /**
             * If token is still valid the key username will be present in the
             * array thet was returned
             */
            if (array_key_exists('username', $loggedin)) {
                $request = $request->withAttribute('username', $loggedin['username']);
                $response = $next($request, $response);
            } else {
                $response = $response->withHeader('Content-type', 'application/json');
                $response = $response->withStatus(401);
                $message = [
                    'message' => 'Your token has either expired or invalid. Please login to get the correct token'
                ];
                $response->write(json_encode($message));
            }
        } else {
            $response = $response->withStatus(401);
            $message = [
                'message' => 'Your token is invalid. Please login to get the correct token'
            ];
            $response = $response->withHeader('Content-type', 'application/json');
            $response->write(json_encode($message));
        }


    } else {
        $response = $response->withStatus(401);
        $response = $response->withHeader('Content-type', 'application/json');
        $message = [
            'message' => 'Please provide an authentication token',
        ];
        $response->write(json_encode($message));
    }

    return $response;
};
