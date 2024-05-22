<?php

namespace App\Entity;

use App\Enum\CustomAttributeType;
use App\Repository\CustomAttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomAttributeRepository::class)]
class CustomAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    #[Assert\Type(type: CustomAttributeType::class)]
    private ?CustomAttributeType $type = null;

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

    public function getType(): ?CustomAttributeType
    {
        return $this->type;
    }

    public function setType(CustomAttributeType $type): static
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
