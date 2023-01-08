<?php

namespace App\Card;

use App\Answer\AnswerEntity;
use App\Deck\DeckEntity;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\SerializedPath;


/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 * @ORM\Table(name="card")
 * @ORM\HasLifecycleCallbacks
 */
class CardEntity {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $easiness = 1.2;

    /**
     * @ORM\Column(type="integer")
     */
    private $quality = 1;

    /**
     * @ORM\Column(type="integer")
     */
    private $interval = 1;

    /**
     * @ORM\Column(type="integer")
     */
    private $repetitions = 1;

    /**
     * @ORM\Column(type="date")
     */
    private $nextPracticeDate;

    /**
     * @ORM\Column(type="datetime_immutable",  options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=DeckEntity::class, inversedBy="cards")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Ignore()
     */
    private $deck;

    /**
     * @ORM\OneToMany(targetEntity=AnswerEntity::class, mappedBy="card", cascade={"persist"})
     */
    private $answers = [];

    /**
     * @ORM\Column(type="text")
     *
     */
    private $question;

    public function __construct() {
        $this->answers = new ArrayCollection();
    }

    public function getEasiness(): ?float {
        return $this->easiness;
    }

    public function setEasiness(float $easiness): self {
        $this->easiness = $easiness;

        return $this;
    }

    public function getQuality(): ?int {
        return $this->quality;
    }

    public function setQuality(int $quality): self {
        $this->quality = $quality;

        return $this;
    }

    public function getInterval(): ?int {
        return $this->interval;
    }

    public function setInterval(int $interval): self {
        $this->interval = $interval;

        return $this;
    }

    public function getRepetitions(): ?int {
        return $this->repetitions;
    }

    public function setRepetitions(int $repetitions): self {
        $this->repetitions = $repetitions;

        return $this;
    }

    public function getNextPracticeDate(): ?DateTimeInterface {
        return $this->nextPracticeDate;
    }

    public function setNextPracticeDate(DateTimeInterface $nextPracticeDate
    ): self {
        $this->nextPracticeDate = $nextPracticeDate;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAnswers(): Collection {
        return $this->answers;
    }

    public function addAnswer(AnswerEntity $answer): self {
        if(!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setCard($this);
        }

        return $this;
    }

    public function removeAnswer(AnswerEntity $answer): self {
        if($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if($answer->getCard() === $this) {
                $answer->setCard(null);
            }
        }

        return $this;
    }

    public function getQuestion(): ?string {
        return $this->question;
    }

    public function setQuestion(string $question): self {
        $this->question = $question;

        return $this;
    }

    /**
     * This getter is to ensure that we don't not a circular reference
     * when serializing a CardEntity. Instead of an entire deck we return
     * its id
     *
     * @Groups({"deck:id"})
     * @SerializedName("deckId")
     */
    public function getDeckId(): ?int {
        return $this->getDeck()->getId();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getDeck(): ?DeckEntity {
        return $this->deck;
    }

    public function setDeck(?DeckEntity $deck): self {
        $this->deck = $deck;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist() {
        $this->setCreatedAt(new DateTimeImmutable());
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdated() {
        $this->setUpdatedAt(new DateTimeImmutable());
    }
}
