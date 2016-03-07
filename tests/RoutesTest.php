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
    protected $data = [];

    protected function setUp()
    {
        //Create a new guzzleHttp client
        $this->client = new Client([
            'base_uri' => $this->url,
        ]);

        $this->faker = Factory::create();
        $this->data['username'] = $this->faker->username;


        //Login user to get token for other operations during test
        $ruser = [
            'username' => 'vundi',
            'password' => 'password'
        ];

        $response = $this->client->post('/auth/login', ['form_params' => $ruser]);
        $this->data['token'] = json_decode($response->getBody())->token;
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
     * Test empty parameters throws a 400 bad request.
     *
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testRegisterRouteEmptyParams()
    {
        $response = $this->client->post('/auth/register');
    }


    /**
     * Test to see the registration process is handled accordingly
     */
    public function testUserCanSignUp()
    {
        $user = [
            'username' => $this->data['username'],
            'password' => 'password'
        ];

        $response = $this->client->post('/auth/register', ['form_params' => $user]);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * Assert that exception will be thrown when someone without an
     * account tries to login
     *
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testLoginForUnregisteredUser()
    {
        $newuser = [
            'username' => 'kimendjd',
            'password' => 'nopass'
        ];

        $response = $this->client->post('/auth/login', ['form_params' => $newuser]);
    }

    /**
     * Assert that exception will be thrown when someone without an
     * account tries to login
     */
    public function testLoginForExistingUser()
    {
        $ruser = [
            'username' => 'vundi',
            'password' => 'password'
        ];

        $response = $this->client->post('/auth/login', ['form_params' => $ruser]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
        $this->data['token'] = json_decode($response->getBody())->token;
        $this->assertEquals('string', gettype($this->data['token']));
    }

    /**
     * Testing that error is thrown when someone types in the
     * wrong password
     * @expectedException GuzzleHttp\Exception\ClientException
     */

    public function testLoginWithWrongCredentials()
    {
        $ruser = [
            'username' => 'vundi',
            'password' => 'passwosjsrd'
        ];

        $response = $this->client->post('/auth/login', ['form_params' => $ruser]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test logging out does not work when token is not supplied
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testLogout()
    {
        $response = $this->client->get('/auth/logout');
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test logging out works when token is supplied in the header
     */
    public function testLogoutWorksWhenTokenIsProvided()
    {
        $response = $this->client->get('/auth/logout', [
            'headers' => [
                'token' => $this->data['token']
            ]
         ]);
         $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test One can see all emojis
     */
    public function testOneCanSeeAllEmojis()
    {
        $response = $this->client->get('/emojis');
        $this->assertEquals(200, $response->getStatusCode());

        $emojis = json_decode($response->getBody(), true);
        $this->assertTrue(is_array($emojis));
    }

    /**
     * Test logged in user can create an emoji when token is
     * valid
     */
    public function testLoggedInUserCanCreateAnEmoji()
    {
        $response = $this->client->post('/emoji', [
            'headers' => [
                'token' => $this->data['token']
            ]
         ]);
    }
}
