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
     * @ORM\Column(type="array", nullable=true)
     */
    private $reasons = [];

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

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $create_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $modify_count;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $delete_count;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $harmful;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $suspect;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checked;

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

    public function setEditor(?string $editor): self
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

    public function getReasons(): ?array
    {
        return $this->reasons;
    }

    public function setReasons(?array $reasons): self
    {
        $this->reasons = $reasons;

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

    public function getCreateCount(): ?int
    {
        return $this->create_count;
    }

    public function setCreateCount(?int $create_count): self
    {
        $this->create_count = $create_count;

        return $this;
    }
    public function getModifyCount(): ?int
    {
        return $this->modify_count;
    }

    public function setModifyCount(?int $modify_count): self
    {
        $this->modify_count = $modify_count;

        return $this;
    }

    public function getDeleteCount(): ?int
    {
        return $this->delete_count;
    }

    public function setDeleteCount(?int $delete_count): self
    {
        $this->delete_count = $delete_count;

        return $this;
    }

    public function getHarmful(): ?bool
    {
        return $this->harmful;
    }

    public function setHarmful(?bool $harmful): self
    {
        $this->harmful = $harmful;

        return $this;
    }

    public function getSuspect(): ?bool
    {
        return $this->suspect;
    }

    public function setSuspect(?bool $suspect): self
    {
        $this->suspect = $suspect;

        return $this;
    }

    public function getChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(?bool $checked): self
    {
        $this->checked = $checked;

        return $this;
    }
}
