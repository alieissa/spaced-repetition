<?php

namespace App\Answer;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnswerTest extends WebTestCase {
    public function testNewAnswer()
    : void {
        $data = ["content" => "test answer"];

        $client = static::createClient();
        $client->request(
            'POST',
            '/card/11/answer',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['content'], $responseData['content']);
        $this->assertResponseIsSuccessful();
    }


    public function testEditAnswer() {
        $data = ['content' => 'edited answer'];

        $client = static::createClient();

        $client->request(
            'PUT',
            '/card/11/answer/14',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['content'], $responseData['content']);
        $this->assertResponseIsSuccessful();
    }


    public function testRemoveAnswer() {
        $client = static::createClient();

        $client->request(
            'DELETE',
            '/card/11/answer/16',
        );

        $this->assertResponseIsSuccessful();
    }
}
