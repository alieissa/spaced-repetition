<?php

namespace App\Deck;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeckTest extends WebTestCase
{
    public function testGetDecks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/deck');

        $this->assertResponseIsSuccessful();
    }

    public function testNewDeck()
    {
        $data = ['name' => 'test deck'];

        $client = static::createClient();

        $client->request(
            'POST',
            '/deck',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testShowDeck()
    {
        $client = static::createClient();
        // TODO Make sure we have a deck with a set id in test db. Needed for testing
        $client->request('GET', '/deck/11');
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(11, $responseData['id']);
        $this->assertResponseIsSuccessful();
    }

    public function testDeckNotFound()
    {
        $client = static::createClient();
        $client->request('GET', '/deck/1234');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditDeck()
    {
        $data = ['name' => 'edited test deck'];

        $client = static::createClient();

        $client->request(
            'PUT',
            '/deck/11',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteDeck()
    {
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/deck/11',
        );

        $this->assertResponseIsSuccessful();
    }
}
