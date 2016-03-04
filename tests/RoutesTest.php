<?php

namespace Vundi\NaEmoji\Test;

use Vundi\NaEmoji\Controllers\EmojiController;
use Vundi\NaEmoji\Models\Emoji;
use Vundi\NaEmoji\Models\User;
use GuzzleHttp\Client;
use PHPUnit_Framework_TestCase;

class RoutesTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    protected $url = 'https://naemoji-staging.herokuapp.com';

   //setup guzzle client
    protected function setUp()
    {
        $this->client = new Client([
            'base_uri' => $this->url,
        ]);
    }
    /**
     * Test landing can be accessed successfully returning
     * response with status 200
     */
    public function testLandingPage()
    {
        $response = $this->client->get('/');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
