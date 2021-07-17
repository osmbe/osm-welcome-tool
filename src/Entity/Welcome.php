<?php

namespace App\Entity;

use App\Repository\WelcomeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WelcomeRepository::class)
 */
class Welcome
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $uid;

    /**
     * @ORM\Column(type="integer")
     */
    private $by_uid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $by_display_name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUId(): ?int
    {
        return $this->uid;
    }

    public function setUId(int $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    public function getByUId(): ?int
    {
        return $this->by_uid;
    }

    public function setByUId(int $by_uid): self
    {
        $this->by_uid = $by_uid;

        return $this;
    }

    public function getByDisplayName(): ?string
    {
        return $this->by_display_name;
    }

    public function setByDisplayName(string $by_display_name): self
    {
        $this->by_display_name = $by_display_name;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }
}
