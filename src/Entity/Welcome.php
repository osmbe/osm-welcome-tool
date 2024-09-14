<?php

namespace App\Entity;

use App\Repository\WelcomeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WelcomeRepository::class)]
class Welcome
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToOne(targetEntity: Mapper::class, inversedBy: 'welcome', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Mapper $mapper;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $date;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $reply;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

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

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getReply(): ?\DateTime
    {
        return $this->reply;
    }

    public function setReply(?\DateTime $reply): self
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
