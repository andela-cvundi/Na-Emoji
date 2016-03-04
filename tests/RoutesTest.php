<?php

namespace Vundi\NaEmoji\Test;

use Vundi\NaEmoji\Controllers\EmojiController;
use Vundi\NaEmoji\Models\Emoji;
use Vundi\NaEmoji\Models\User;
use GuzzleHttp\Client;
use Faker\Factory;
use PHPUnit_Framework_TestCase;

class RoutesTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    protected $url = 'http://naemoji.dev';

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

    /**
     * Test to see the registration process is handled accordingly
     */
    public function testUserIsRegistered()
    {
        $response = $this->client->request('POST', '/auth/register', [
            'json' => [
                'username'  => 'vundi',
                'password'  => 'christopher'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertInternalType('array', $data);
    }
}
