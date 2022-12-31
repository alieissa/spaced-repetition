<?php

namespace App\Tests\Card;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CardTest extends WebTestCase
{
    public function testGetCards(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card');

        $this->assertResponseIsSuccessful();
    }

    public function testNewCard()
    {
        $data = [
            'question' => 'test card',
            "answers"  => [["content" => "text"]],
        ];

        $client = static::createClient();

        $client->request(
            'POST',
            '/deck/11/card',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['question'], $responseData['question']);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testShowCard()
    {
        $client = static::createClient();
        /**
         * TODO Make sure we have a deck and a card with a set ids in test db. Needed for testing
         *
         */
        $client->request('GET', '/deck/11/card/11');
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(11, $responseData['id']);
        $this->assertResponseIsSuccessful();
    }

    public function testCardQuestionUpdate()
    {
        $data = ['question' => 'edited card question'];

        $client = static::createClient();

        $client->request(
            'PUT',
            '/deck/11/card/11/question-update',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['question'], $responseData['question']);
        $this->assertResponseIsSuccessful();
    }

    public function testCardQualityUpdate()
    {
        $data = ['quality' => 2];

        $client = static::createClient();

        $client->request(
            'PUT',
            '/deck/11/card/11/quality-update',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['quality'], $responseData['quality']);
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteCard()
    {
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/deck/11/card/11',
        );

        $this->assertResponseIsSuccessful();
    }
}

