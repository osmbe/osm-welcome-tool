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
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\OneToOne(targetEntity=Mapper::class, mappedBy="welcome", cascade={"persist", "remove"})
     */
    private $mapper;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMapper(): ?Mapper
    {
        return $this->mapper;
    }

    public function setMapper(?Mapper $mapper): self
    {
        // unset the owning side of the relation if necessary
        if ($mapper === null && $this->mapper !== null) {
            $this->mapper->setWelcome(null);
        }

        // set the owning side of the relation if necessary
        if ($mapper !== null && $mapper->getWelcome() !== $this) {
            $mapper->setWelcome($this);
        }

        $this->mapper = $mapper;

        return $this;
    }
}
