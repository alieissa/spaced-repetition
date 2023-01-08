<?php

namespace App\Deck;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DeckTest extends WebTestCase {
    private DeckEntity $deck;
    private KernelBrowser $client;
    private string $baseUrl = '/deck';

    public function setUp(): void {
        // DAMA bundle ensures that operations are not persisted to db.
        // There is no for a tear down method to undo them.
        $deckRepository = self::getContainer()->get('App\Deck\DeckRepository');
        $deck = (new DeckEntity())->setName('test deck');
        $deckRepository->add($deck, true);
        $this->deck = $deck;

        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }


    public function testGetDecks(): void {
        $this->client->request('GET', $this->baseUrl);

        $this->assertResponseIsSuccessful();
    }

    public function testNewDeck() {
        $data = ['name' => 'test deck'];

        $this->client->request(
            'POST', $this->baseUrl, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );

        $responseData = $this->getDecodedResponseContent();
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testShowDeck() {
        $id = $this->deck->getId();
        $this->client->request('GET', $this->getTestUrl());
        $responseData = $this->getDecodedResponseContent();

        $this->assertEquals($id, $responseData['id']);
        $this->assertResponseIsSuccessful();
    }

    public function testDeckNotFound() {
        $this->client->request('GET', '/deck/-1234');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditDeck() {
        $data = ['name' => 'edited test deck'];

        $this->client->request(
            'PUT', $this->getTestUrl(), [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );

        $responseData = $this->getDecodedResponseContent();
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteDeck() {
        $this->client->request(
            'DELETE', $this->getTestUrl(),
        );

        $this->assertResponseIsSuccessful();
    }

    private function getTestUrl() {
        $id = $this->deck->getId();
        return sprintf('%s/%d', $this->baseUrl, $id);
    }

    private function getDecodedResponseContent() {
        $content = $this->client->getResponse()->getContent();
        return json_decode($content, true);
    }
}
