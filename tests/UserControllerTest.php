<?php


namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public function testGetUsers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users', [], [], ['HTTP_ACCEPT' => 'application/json']);

        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

        $arrayContent = \json_decode($content, true);
        $this->assertCount(10, $arrayContent);
    }

    public function testPostUsers()
    {
        $client = static::createClient();
        $client->request('POST', '/api/users', [], [],
            [
                'HTTP_ACCEPT' => 'application/json' ,
                'CONTENT_TYPE' => 'application/json' ,
                'HTTP_X-AUTH-TOKEN' => '2981'
            ],
            '{"apiKey": "croi","email": "antelo@gmail.com", "firstname": "ante",
                      "lastname": "loco", "birthday": "1994-03-21T00:00:00+05:00"}'
        );
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);
    }






}
