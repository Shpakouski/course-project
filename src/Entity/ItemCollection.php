<?php

namespace App\Entity;

use App\Repository\ItemCollectionRepository;
use App\Validator\CollectionCustomAttribute;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemCollectionRepository::class)]
class ItemCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\ManyToOne(inversedBy: 'itemCollections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'itemCollections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, CustomAttribute>
     */
    #[ORM\OneToMany(targetEntity: CustomAttribute::class, mappedBy: 'ItemCollection', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid]
    #[CollectionCustomAttribute(maxItemPerType: 1)]
    private Collection $customAttributes;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'itemCollection', orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->customAttributes = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, CustomAttribute>
     */
    public function getCustomAttributes(): Collection
    {
        return $this->customAttributes;
    }

    public function addCustomAttribute(CustomAttribute $customAttribute): static
    {
        if (!$this->customAttributes->contains($customAttribute)) {
            $this->customAttributes->add($customAttribute);
            $customAttribute->setItemCollection($this);
        }

        return $this;
    }

    public function removeCustomAttribute(CustomAttribute $customAttribute): static
    {
        if ($this->customAttributes->removeElement($customAttribute)) {
            // set the owning side to null (unless already changed)
            if ($customAttribute->getItemCollection() === $this) {
                $customAttribute->setItemCollection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setItemCollection($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getItemCollection() === $this) {
                $item->setItemCollection(null);
            }
        }

        return $this;
    }
}
