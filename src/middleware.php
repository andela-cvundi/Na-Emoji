<?php
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
        if (array_key_exists('username', $user)) {
            $request = $request->withAttribute('username', $user['username']);
            $response = $next($request, $response);
        } else {
            $response = $response->withStatus(401);
            $message = [
                'message' => 'Your token is either expired or invalid. Please login to get the correct token',
            ];
            $json = json_encode($message);
            $response->write($json);
        }
    } else {
        $response = $response->withStatus(401);
        $message = [
            'message' => 'Please provide an authentication token',
        ];
        $json = json_encode($message);
        $response->write($json);
    }
    return $response;
};
