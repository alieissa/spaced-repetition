<?php

namespace App\Deck;

use App\Card\CardEntity;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeckRepository::class)
 * @ORM\Table(name="deck")
 * @ORM\HasLifecycleCallbacks()
 */
class DeckEntity {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\GeneratedValue
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=CardEntity::class, mappedBy="deck", cascade={"persist"})
     */
    private $cards;

    public function __construct() {
        $this->cards = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

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

    /**
     * @return Collection<int, CardEntity>
     */
    public function getCards(): Collection {
        return $this->cards;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function addCard(CardEntity $card): self {
        if(!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setDeck($this);
        }

        return $this;
    }

    public function removeCard(CardEntity $card): self {
        if($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if($card->getDeck() === $this) {
                $card->setDeck(null);
            }
        }

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
