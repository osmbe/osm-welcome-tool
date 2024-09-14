<?php

namespace App\Entity;

use App\Repository\RegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private ?string $id;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $lastUpdate;

    #[ORM\ManyToMany(targetEntity: Mapper::class, mappedBy: 'region')]
    private Collection $mappers;

    public function __construct()
    {
        $this->mappers = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTime $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * @return Collection<int, Mapper>
     */
    public function getMappers(): Collection
    {
        return $this->mappers;
    }

    public function addMapper(Mapper $mapper): self
    {
        if (!$this->mappers->contains($mapper)) {
            $this->mappers->add($mapper);
            $mapper->addRegion($this);
        }

        return $this;
    }

    public function removeMapper(Mapper $mapper): self
    {
        if ($this->mappers->removeElement($mapper)) {
            $mapper->removeRegion($this);
        }

        return $this;
    }
}
