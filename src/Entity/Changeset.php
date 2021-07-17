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
     * @ORM\Column(type="string", length=255)
     */
    private $uid;

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
     * @ORM\Column(type="integer")
     */
    private $create_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $modify_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $delete_count;

    /**
     * @ORM\Column(type="boolean")
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

    public function getUId(): ?string
    {
        return $this->uid;
    }

    public function setUId(string $uid): self
    {
        $this->uid = $uid;

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

    public function getCreateCount(): ?int
    {
        return $this->create_count;
    }

    public function setCreateCount(int $create_count): self
    {
        $this->create_count = $create_count;

        return $this;
    }

    public function getModifyCount(): ?int
    {
        return $this->modify_count;
    }

    public function setModifyCount(int $modify_count): self
    {
        $this->modify_count = $modify_count;

        return $this;
    }

    public function getDeleteCount(): ?int
    {
        return $this->delete_count;
    }

    public function setDeleteCount(int $delete_count): self
    {
        $this->delete_count = $delete_count;

        return $this;
    }

    public function getChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): self
    {
        $this->checked = $checked;

        return $this;
    }
}
