<?php

namespace App\Card;

use App\Deck\DeckEntity;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CardTest extends WebTestCase {
    public function setUp(): void {
        $deckRepository = self::getContainer()->get('App\Deck\DeckRepository');
        $deck = (new DeckEntity())->setName('test deck');
        $card = (new CardEntity())
            ->setQuestion('test question')
            ->setNextPracticeDate(new DateTimeImmutable());

        $deck->addCard($card);
        $deckRepository->add($deck, true);
        $this->card = $card;
        $this->deck = $deck;

        $this->baseUrl = sprintf('/deck/%d/card', $deck->getId());

        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    public function testGetCards(): void {
        $this->client->request('GET', '/deck/-/card');

        $this->assertResponseIsSuccessful();
    }

    public function testNewCard() {
        $data = [
            'question' => 'test card',
            "answers"  => [["content" => "text"]],
        ];

        $this->client->request(
            'POST', $this->baseUrl, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );

        $responseData = $this->getDecodedResponseContent();
        $this->assertEquals($data['question'], $responseData['question']);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCardQuestionUpdate() {
        $data = ['question' => 'edited card question'];
        $url = sprintf('%s/question-update', $this->getTestUrl());
        $this->client->request(
            'PUT', $url, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );

        $responseData = $this->getDecodedResponseContent();
        $this->assertEquals($data['question'], $responseData['question']);
        $this->assertResponseIsSuccessful();
    }

    public function testCardQualityUpdate() {
        $data = ['quality' => 2];
        $url = sprintf('%s/quality-update', $this->getTestUrl());
        $this->client->request(
            'PUT', $url, [], [],
            ['CONTENT_TYPE' => 'application/json'], json_encode($data)
        );

        $responseData = $this->getDecodedResponseContent();
        $this->assertEquals($data['quality'], $responseData['quality']);
        $this->assertResponseIsSuccessful();
    }

    public function testRemoveCard() {
        $this->client->request('DELETE', $this->getTestUrl());
        $this->assertResponseIsSuccessful();
    }

    private function getTestUrl() {
        return sprintf(
            '%s/%d', $this->baseUrl, $this->card->getId()
        );
    }

    private function getDecodedResponseContent() {
        $content = $this->client->getResponse()->getContent();
        return json_decode($content, true);
    }
}

