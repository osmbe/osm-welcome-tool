<?php

namespace App\Entity;

use App\Repository\ChangesetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChangesetRepository::class)
 */
class Changeset
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $editor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="array")
     */
    private $tags = [];

    /**
     * @ORM\ManyToOne(targetEntity=Mapper::class, inversedBy="changesets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mapper;

    /**
     * @ORM\Column(type="integer")
     */
    private $changes_count;

    /**
     * @ORM\Column(type="array")
     */
    private $extent = [];

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locale;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(string $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getMapper(): ?Mapper
    {
        return $this->mapper;
    }

    public function setMapper(?Mapper $mapper): self
    {
        $this->mapper = $mapper;

        return $this;
    }

    public function getChangesCount(): ?int
    {
        return $this->changes_count;
    }

    public function setChangesCount(int $changes_count): self
    {
        $this->changes_count = $changes_count;

        return $this;
    }

    public function getExtent(): ?array
    {
        return $this->extent;
    }

    public function setExtent(array $extent): self
    {
        $this->extent = $extent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

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
}
