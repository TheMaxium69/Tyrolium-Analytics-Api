<?php

namespace App\Entity;

use App\Repository\InputRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InputRepository::class)]
class Input
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Projects $tag = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $ip = null;

    #[ORM\Column(length: 255)]
    private ?string $page_name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $uri = null;

    #[ORM\Column]
    private ?bool $isLogin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTagId(): ?Projects
    {
        return $this->tag;
    }

    public function setTagId(?Projects $tag_id): static
    {
        $this->tag = $tag_id;

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
        return $this->isLogin;
    }

    public function setIsLogin(bool $isLogin): static
    {
        $this->isLogin = $isLogin;

        return $this;
    }
}
