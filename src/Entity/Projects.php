<?php

namespace App\Entity;

use App\Repository\ProjectsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectsRepository::class)]
class Projects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:project'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['post:tag', 'get:project'])]
    private ?string $tag = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['get:project'])]
    private array $domain_names = [];

    #[ORM\Column]
    #[Groups(['get:project'])]
    private ?int $useritium_id = null;

    #[ORM\Column]
    #[Groups(['get:project'])]
    private ?\DateTimeImmutable $created_At = null;

    public function __construct(){
        $this->created_At = new \DateTimeImmutable();
    }

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_At;
    }

    public function setCreatedAt(\DateTimeImmutable $created_At): static
    {
        $this->created_At = $created_At;

        return $this;
    }

    public function getUseritiumId(): ?int
    {
        return $this->useritium_id;
    }

    public function setUseritiumId(int $useritium_id): static
    {
        $this->useritium_id = $useritium_id;

        return $this;
    }
}
