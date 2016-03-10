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
    protected $url = 'https://naemoji-staging.herokuapp.com';
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
     * Test get one emoji
     */
    public function testGetOneEmoji()
    {
        $response = $this->client->get('/emoji/1');
        $this->assertEquals(200, $response->getStatusCode());
        $emoji = json_decode($response->getBody(), true);
        $this->assertTrue(is_array($emoji));
    }

    /**
     * Test logged in user can create an emoji when token is
     * valid
     */
    public function testLoggedInUserCanCreateAnEmoji()
    {
        $emoji = [
            'name' => 'vundi',
            'char' => ':-)',
            'category' => 'keywords',
            'keywords' => 'water', 'alien'
        ];

        $response = $this->client->post('/emoji', [
            'headers' => [
                'token' => $this->data['token']
            ],
            'form_params' => $emoji
         ]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * Test valid token is required to create an Emoji
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testTokenIsRequiredToCreateAnEmoji()
    {
        $emoji = [
            'name' => 'vundi',
            'char' => ':-)',
            'category' => 'keywords',
            'keywords' => 'water', 'alien'
        ];

        $response = $this->client->post('/emoji', [
            'form_params' => $emoji
         ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test One has to fill in all fields before creating an Emoji
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testOneHasToFillRequiredFields()
    {
        $emoji = [
            'keywords' => 'water', 'alien'
        ];

        $response = $this->client->post('/emoji', [
            'headers' => [
                'token' => $this->data['token']
            ],
            'form_params' => $emoji
         ]);
    }

    /**
     * Test Put works well when all fields are provided and token is supplied
     */
    public function testPutWorks()
    {
        $emoji = [
            'name' => 'modified',
            'char' => ':-)',
            'category' => 'keywords',
            'keywords' => 'water', 'alien'
        ];

        $response = $this->client->put('/emoji/1', [
            'headers' => [
                'token' => $this->data['token']
            ],
            'form_params' => $emoji
         ]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * Test Put must be supplied with all emoji fields for a successful
     * update to happen
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testAllEmojiFieldsMustBeSuppliedForPut()
    {
        $emoji = [
            'name' => 'modified',
            'char' => ':-)',
            'keywords' => 'water', 'alien'
        ];

        $response = $this->client->put('/emoji/1', [
            'headers' => [
                'token' => $this->data['token']
            ],
            'form_params' => $emoji
         ]);
    }

    /**
     * Test valid token must be supplied for put to work
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testTokenIsRequiredForPutToWork()
    {
        $emoji = [
            'name' => 'vundi',
            'char' => ':-)',
            'category' => 'keywords',
            'keywords' => 'water', 'alien'
        ];

        $response = $this->client->put('/emoji/1', [
            'form_params' => $emoji
         ]);

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test patch works when valid token is supplied
     */
    public function testPatchWorksWhenAValidTokenIsSupplied()
    {
        $emoji = [
            'name' => 'patched'
        ];

        $response = $this->client->patch('/emoji/1', [
            'headers' => [
                'token' => $this->data['token']
            ],
            'form_params' => $emoji
         ]);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * Test patch must be supplied with atleast one key value pair to update
     */
    public function testPatchNeedsAtleastOneKeyValuePair()
    {
        $response = $this->client->patch('/emoji/1', [
            'headers' => [
                'token' => $this->data['token']
            ],
            'form_params' => []
         ]);

        $this->assertEquals(304, $response->getStatusCode());

    }

    /**
     * Test delete request cannot work when token is not supplied
     * @expectedException GuzzleHttp\Exception\ClientException
     */
    public function testDeleteMustBeSuppliedWithAToken()
    {
        $response = $this->client->delete('/emoji/1');
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test delete request works when a valid token is supplied
     */
    public function testDeleteMustBeSuppliedWithAValidToken()
    {
        $response = $this->client->delete('/emoji/$this->id', [
            'headers' => [
                'token' => $this->data['token']
            ]
         ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test cannot delete an ID that is non-existent
     */
    public function testErrorIsThrownWhenSomeoneTriesToDeleteANonExistentEmoji()
    {
        $response = $this->client->delete('/emoji/4', [
            'headers' => [
                'token' => $this->data['token']
            ]
         ]);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
