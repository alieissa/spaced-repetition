<?php

namespace App\Answer;

use App\Card\CardEntity;
use App\Deck\DeckEntity;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnswerTest extends WebTestCase {
    public function setUp(): void {
        $deckRepository = self::getContainer()->get('App\Deck\DeckRepository');
        $answer = (new AnswerEntity())->setContent('test answer');
        $card = (new CardEntity())
            ->setQuestion('test question')
            ->setNextPracticeDate(new DateTimeImmutable())
            ->addAnswer($answer);
        $deck = (new DeckEntity())->setName('test deck');

        $deck->addCard($card);
        $deckRepository->add($deck, true);
        $this->answer = $answer;
        $this->card = $card;
        $this->deck = $deck;

        $this->baseUrl = sprintf('/card/%d/answer', $card->getId());

        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }


    public function testNewAnswer(): void {
        $data = ["content" => "test answer"];

        $this->client->request(
            'POST',
            $this->baseUrl,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $responseData = $this->getDecodedResponseContent();
        $this->assertEquals($data['content'], $responseData['content']);
        $this->assertResponseIsSuccessful();
    }


    public function testEditAnswer() {
        $data = ['content' => 'edited answer'];

        $this->client->request(
            'PUT',
            $this->getTestUrl(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = $this->getDecodedResponseContent();
        $this->assertEquals($data['content'], $responseData['content']);
        $this->assertResponseIsSuccessful();
    }


    public function testRemoveAnswer() {
        $this->client->request('DELETE', $this->getTestUrl());
        $this->assertResponseIsSuccessful();
    }

    private function getTestUrl() {
        return sprintf(
            '%s/%d', $this->baseUrl, $this->answer->getId()
        );
    }

    private function getDecodedResponseContent() {
        $content = $this->client->getResponse()->getContent();
        return json_decode($content, true);
    }
}
