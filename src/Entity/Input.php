<?php

namespace App\Entity;

use App\Repository\InputRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: InputRepository::class)]
class Input
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['input:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['input:read'])]
    private ?Projects $project = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['input:read'])]
    private ?string $ip = null;

    #[ORM\Column(length: 255)]
    #[Groups(['input:read'])]
    private ?string $page_name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['input:read'])]
    private ?string $uri = null;

    #[ORM\Column]
    #[Groups(['input:read'])]
    private ?bool $is_login = null;

    #[ORM\Column]
    #[Groups(['input:read'])]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct(){
        $this->created_at = new \DateTimeImmutable();
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

    public function getProject(): ?Projects
    {
        return $this->project;
    }

    public function setProject(?Projects $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getPageName(): ?string
    {
        return $this->page_name;
    }

    public function setPageName(string $page_name): static
    {
        $this->page_name = $page_name;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): static
    {
        $this->uri = $uri;

        return $this;
    }

    public function isLogin(): ?bool
    {
        return $this->is_login;
    }

    public function setIsLogin(bool $is_login): static
    {
        $this->is_login = $is_login;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
