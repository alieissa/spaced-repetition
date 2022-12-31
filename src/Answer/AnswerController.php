<?php

namespace App\Answer;

use App\Card\CardEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AnswerController extends AbstractController {
    private AnswerRepository $answerRepository;
    private SerializerInterface $serializer;

    public function __construct(
        AnswerRepository $answerRepository,
        SerializerInterface $serializer
    ) {
        $this->answerRepository = $answerRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/card/{card}/answer", methods={"POST"})
     */
    public function new(CardEntity $card, Request $request): Response {
        $data = $request->getContent();
        $answer = $this->serializer->deserialize(
            $data, AnswerEntity::class, 'json'
        );

        $card->addAnswer($answer);
        $this->answerRepository->add(
            $answer, true
        );

        return $this->json(
            $answer, Response::HTTP_CREATED
        );
    }

    /**
     * @Route("/card/{cardId}/answer/{id}", methods={"PUT"})
     */
    public function edit(AnswerEntity $answer, Request $request): Response {
        $data = $request->toArray();
        $answer->setContent($data['content']);

        $this->answerRepository->add(
            $answer, true
        );

        return $this->json(
            $answer, Response::HTTP_ACCEPTED
        );
    }

    /**
     * @Route("/card/{card}/answer/{id}", methods={"DELETE"})
     */
    public function remove(AnswerEntity $answer): Response {
        $this->answerRepository->remove(
            $answer, true
        );
        return new Response(Response::HTTP_OK);
    }
}
