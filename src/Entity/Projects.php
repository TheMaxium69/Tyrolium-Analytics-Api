<?php

namespace App\Entity;

use App\Repository\PprojectsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PprojectsRepository::class)]
class projects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tag = null;

    #[ORM\Column]
    private array $domain_names = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    public function getDomainNames(): array
    {
        return $this->domain_names;
    }

    public function setDomainNames(array $domain_names): static
    {
        $this->domain_names = $domain_names;

        return $this;
    }
}
