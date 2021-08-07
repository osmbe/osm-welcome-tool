<?php

namespace App\Entity;

use App\Repository\MapperRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Changeset::class, mappedBy="mapper", orphanRemoval=true)
     */
    private $changesets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="mapper", orphanRemoval=true)
     */
    private $notes;

    public function __construct()
    {
        $this->changesets = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Changeset[]
     */
    public function getChangesets(): Collection
    {
        return $this->changesets;
    }

    public function addChangeset(Changeset $changeset): self
    {
        if (!$this->changesets->contains($changeset)) {
            $this->changesets[] = $changeset;
            $changeset->setMapper($this);
        }

        return $this;
    }

    public function removeChangeset(Changeset $changeset): self
    {
        if ($this->changesets->removeElement($changeset)) {
            // set the owning side to null (unless already changed)
            if ($changeset->getMapper() === $this) {
                $changeset->setMapper(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setMapper($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getMapper() === $this) {
                $note->setMapper(null);
            }
        }

        return $this;
    }
}
