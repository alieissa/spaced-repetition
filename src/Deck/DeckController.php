<?php

namespace App\Deck;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DeckController extends AbstractController {
    private DeckRepository $deckRepository;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(
        DeckRepository $deckRepository,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->deckRepository = $deckRepository;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @Route("/deck", name="app_deck_index", methods={"GET"})
     */
    public function index(): Response {
        $data = $this->deckRepository->findAll();
        return $this->json($data);
    }

    /**
     * @Route("/deck", name="app_deck_new", methods={"POST"})
     */
    public function new(Request $request): Response {
        $data = $request->getContent();

        $deck = $this->serializer->deserialize(
            $data, DeckEntity::class, 'json'
        );
        $this->deckRepository->add($deck, true);

        return $this->json($deck, Response::HTTP_CREATED);
    }

    /**
     * @Route("deck/{id}", name="app_deck_show", methods={"GET"})
     */
    public function show(DeckEntity $deck): Response {
        return $this->json($deck);
    }

    /**
     * @Route("/deck/{id}", name="app_deck_edit", methods="PUT")
     */
    public function edit(Request $request, DeckEntity $deck): Response {
        $data = $request->toArray();
        $deck->setName($data['name']);
        $this->deckRepository->add($deck, true);

        return $this->json($deck, Response::HTTP_OK);
    }

    /**
     * @Route("/deck/{id}", name="app_deck_delete", methods="DELETE")
     */
    public function delete(DeckEntity $deck): Response {
        $this->deckRepository->remove($deck, true);
        return $this->json(Response::HTTP_OK);
    }
}
