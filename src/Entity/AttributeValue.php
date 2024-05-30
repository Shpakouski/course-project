<?php

namespace App\Entity;

use App\Enum\CustomAttributeType;
use App\Repository\AttributeValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttributeValueRepository::class)]
class AttributeValue
{
    public const ATTR_PREFIX = 'attr_';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'attributeValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomAttribute $customAttribute = null;

    #[ORM\ManyToOne(inversedBy: 'attributeValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Item $item = null;

    #[ORM\Column(nullable: true)]
    private ?int $intValue = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stringValue = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textValue = null;

    #[ORM\Column(nullable: true)]
    private ?bool $boolValue = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomAttribute(): ?CustomAttribute
    {
        return $this->customAttribute;
    }

    public function setCustomAttribute(?CustomAttribute $customAttribute): static
    {
        $this->customAttribute = $customAttribute;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getIntValue(): ?int
    {
        return $this->intValue;
    }

    public function setIntValue(?int $intValue): static
    {
        $this->intValue = $intValue;

        return $this;
    }

    public function getStringValue(): ?string
    {
        return $this->stringValue;
    }

    public function setStringValue(?string $stringValue): static
    {
        $this->stringValue = $stringValue;

        return $this;
    }

    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    public function setTextValue(?string $textValue): static
    {
        $this->textValue = $textValue;

        return $this;
    }

    public function isBoolValue(): ?bool
    {
        return $this->boolValue;
    }

    public function setBoolValue(?bool $boolValue): static
    {
        $this->boolValue = $boolValue;

        return $this;
    }

    public function getDateValue(): ?\DateTimeInterface
    {
        return $this->dateValue;
    }

    public function setDateValue(?\DateTimeInterface $dateValue): static
    {
        $this->dateValue = $dateValue;

        return $this;
    }

    public function getValue($type): ?string
    {
        return match ($type) {
            CustomAttributeType::Integer->value => $this->intValue,
            CustomAttributeType::Text->value => $this->textValue,
            CustomAttributeType::Boolean->value => $this->boolValue,
            CustomAttributeType::Date->value => $this->dateValue,
            default => $this->stringValue,
        };
    }
}
