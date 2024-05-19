<?php

namespace App\Entity;

use App\Repository\CustomAttributeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomAttributeRepository::class)]
class CustomAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'customAttributes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemCollection $ItemCollection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getItemCollection(): ?ItemCollection
    {
        return $this->ItemCollection;
    }

    public function setItemCollection(?ItemCollection $ItemCollection): static
    {
        $this->ItemCollection = $ItemCollection;

        return $this;
    }
}
