<?php

namespace App\Entity;

use App\Repository\MapperRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MapperRepository::class)
 */
class Mapper
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $display_name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $account_created;

    /**
     * @ORM\Column(type="integer")
     */
    private $changesets_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $first_changeset;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->display_name;
    }

    public function setDisplayName(string $display_name): self
    {
        $this->display_name = $display_name;

        return $this;
    }

    public function getAccountCreated(): ?\DateTimeInterface
    {
        return $this->account_created;
    }

    public function setAccountCreated(\DateTimeInterface $account_created): self
    {
        $this->account_created = $account_created;

        return $this;
    }

    public function getChangesetsCount(): ?int
    {
        return $this->changesets_count;
    }

    public function setChangesetsCount(int $changesets_count): self
    {
        $this->changesets_count = $changesets_count;

        return $this;
    }

    public function getFirstChangeset(): ?int
    {
        return $this->first_changeset;
    }

    public function setFirstChangeset(int $first_changeset): self
    {
        $this->first_changeset = $first_changeset;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
