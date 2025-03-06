<?php

// src/Entity/Infos.php

namespace App\Entity;

use App\Repository\InfosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InfosRepository::class)]
class Infos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 255, nullable: false)]
    private string $user_rank = '';

    #[ORM\Column(length: 255, nullable: false)]
    private string $victoire = '0';

    #[ORM\Column(length: 255, nullable: false)]
    private string $defaite = '0';

    #[ORM\OneToOne(inversedBy: 'infos', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserRank(): string
    {
        return $this->user_rank;
    }

    public function setUserRank(string $user_rank): static
    {
        $this->user_rank = $user_rank;
        return $this;
    }

    public function getVictoire(): string
    {
        return $this->victoire;
    }

    public function setVictoire(string $victoire): static
    {
        $this->victoire = $victoire;
        return $this;
    }

    public function getDefaite(): string
    {
        return $this->defaite;
    }

    public function setDefaite(string $defaite): static
    {
        $this->defaite = $defaite;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
