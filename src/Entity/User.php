<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Infos $infos = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getInfos(): ?Infos
    {
        return $this->infos;
    }

    public function setInfos(Infos $infos): static
    {
        if ($infos->getUser() !== $this) {
            $infos->setUser($this);
        }
        $this->infos = $infos;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->password = $data['password'];
    }

    public function getUserRank(): ?string
    {
        return $this->infos?->getUserRank();
    }

    public function setUserRank(string $userRank): static
    {
        if ($this->infos) {
            $this->infos->setUserRank($userRank);
        }
        return $this;
    }
}
