<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $display_name;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    /**
     * A visual identifier that represents this user.
     *
     * @see Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->display_name;
    }

    /**
     * @see Symfony\Component\Security\Core\User\UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
     *
     * @see Symfony\Component\Security\Core\User\UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see Symfony\Component\Security\Core\User\UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword;
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
}
