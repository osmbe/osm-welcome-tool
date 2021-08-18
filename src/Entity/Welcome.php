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
     * @ORM\OneToOne(targetEntity=Mapper::class, inversedBy="welcome", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $mapper;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $reply;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMapper(): ?Mapper
    {
        return $this->mapper;
    }

    public function setMapper(Mapper $mapper): self
    {
        $this->mapper = $mapper;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getReply(): ?\DateTimeInterface
    {
        return $this->reply;
    }

    public function setReply(?\DateTimeInterface $reply): self
    {
        $this->reply = $reply;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
