<?php

namespace App\Card;

use App\Deck\DeckEntity;
use App\Entity\Answer;
use DateTime;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CardController extends AbstractController
{
    private CardRepository $cardRepository;
    private LoggerInterface $logger;
    private SerializerInterface $serlializer;

    public function __construct(
        CardRepository $cardRepository,
        SerializerInterface $serlializer,
        LoggerInterface $logger
    ) {
        $this->cardRepository = $cardRepository;
        $this->logger = $logger;
        $this->serlializer = $serlializer;
    }

    /**
     * @Route("/card", name="app_card_card", methods={"GET"})
     */
    public function index(): Response
    {
        $data = $this->cardRepository->findAll();
        return $this->json($data);
    }

    /**
     * @Route("/deck/{deck}/card", methods={"POST"})
     */
    public function new(DeckEntity $deck, Request $request)
    {
        $data = $request->getContent();
        $card = $this->serlializer->deserialize(
            $data, CardEntity::class, 'json'
        );

        $card->setNextPracticeDate(
            $this->getNextPracticeDate($card->getInterval())
        );
        $card->setCreatedAt(
            new DateTimeImmutable()
        ); // TODO Update card entity so that createdAt is generated automatically
        $deck->addCard($card);
        $this->cardRepository->add($card, true);

        return $this->json($card, Response::HTTP_CREATED);
    }

    /**
     * @Route("/deck/{deck}/card/{id}", methods={"DELETE"})
     */
    public function remove(CardEntity $card)
    {
        $card->getDeck()->removeCard($card);
        $this->cardRepository->remove($card, true);
        return new Response(Response::HTTP_OK);
    }

    /**
     * @Route("/deck/{deck}/card", methods={"GET"})
     */
    public function show(DeckEntity $deck)
    {
        return $this->json($deck->getCards());
    }

    /**
     * @Route("/deck/{deck}/card/{id}", methods={"PUT"})
     */
    public function edit(CardEntity $card, Request $request)
    {
        $data = $request->getContent();
        $payload = $this->serlializer->deserialize(
            $data, CardEntity::class, 'json'
        );

        $card->setQuestion($payload->getQuestion());
        $card->setUpdatedAt(
            new DateTimeImmutable()
        ); // TODO Update card entity so that updatedAt is updated automatically
        $this->cardRepository->add($card, true);

        return $this->json($card);
    }

    /**
     * @Route("/deck/{deck}/card/{id}/update-quality", methods={"PUT"})
     */
    public function updateQuality(CardEntity $card, Request $request)
    {
        $data = $request->toArray();
        $quality = $data['quality'];

        $easiness = $card->getEasiness();
        $updatedEasiness = $this->getUpdatedEasiness($easiness, $quality);
        $updatedRepetitions = $this->getUpdatedRepetitions($quality, $card->getRepetitions());
        $updatedIntervals = $this->getUpdatedInterval($card->getInterval(), $updatedRepetitions, $easiness);
        $nextPracticeDate = $this->getNextPracticeDate($updatedIntervals);

        $card->setEasiness($updatedEasiness);
        $card->setRepetitions($updatedRepetitions);
        $card->setInterval($updatedIntervals);
        $card->setNextPracticeDate($nextPracticeDate);

        $this->cardRepository->add($card, true);

        return $this->json($card);
    }

    private function getNextPracticeDate(int $interval)
    {
        $nextPracticeDate = (new DateTime("NOW"))->modify(
            sprintf("+%d day", $interval)
        );
        return $nextPracticeDate;
    }

    private function getUpdatedRepetitions(int $repetitions, int $quality)
    {
        return $quality < 3 ? 1 : ($repetitions + 1);
    }
    private function getUpdatedEasiness(float $easiness, int $quality)
    {
        return max(
            1.3, $easiness + 0.1 - (5.0 - $quality) * (0.08 + (5.0 - $quality)
                * 0.02)
        );
    }

    private function getUpdatedInterval(int $interval, int $repetitions,
        float $easiness
    )
    {
        if ($repetitions <= 1) {
            return 1;
        }

        if ($repetitions == 2) {
            return 6;
        }

        return round($interval * $easiness);
    }
}
