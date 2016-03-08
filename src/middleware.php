<?php

use Vundi\NaEmoji\Models\User;
use Vundi\NaEmoji\findWhere;

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
        //
        $date = $date->format('Y-m-d H:i:s');

        $query = "SELECT * FROM users WHERE token = '$token'";
        $find = new findWhere();
        $user = $find->findResults($query);

        $loggedin = [];

        if (! empty($user)) {
            if ($user['token_expire'] > $date) {
                $loggedin = $user;
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
