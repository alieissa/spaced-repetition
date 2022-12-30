<?php

namespace App\Answer;

use App\Card\CardEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
 * @ORM\Table(name="answer")
 */
class AnswerEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity=CardEntity::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Ignore()
     */
    private $card;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }


    /**
     * This getter is to ensure that we don't not a circular reference
     * when serializing an AnswerEntity. Instead of an entire card we return
     * its id
     *
     * @Groups({"card:id"})
     * @SerializedName("cardId")
     */
    public function getCardId()
    {
        return $this->getCard()->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCard(): ?CardEntity
    {
        return $this->card;
    }

    public function setCard(?CardEntity $card): self
    {
        $this->card = $card;

        return $this;
    }
}
