<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['collection:read']],
    denormalizationContext: ['groups' => ['collection:write']],
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    #[Groups(['collection:read', 'collection:write'])]
    private ?string $name = null;

    /**
     * @var Collection<int, ItemCollection>
     */
    #[ORM\OneToMany(targetEntity: ItemCollection::class, mappedBy: 'category', orphanRemoval: true)]
    private Collection $itemCollections;

    public function __construct()
    {
        $this->itemCollections = new ArrayCollection();
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

    /**
     * @return Collection<int, ItemCollection>
     */
    public function getItemCollections(): Collection
    {
        return $this->itemCollections;
    }

    public function addItemCollection(ItemCollection $itemCollection): static
    {
        if (!$this->itemCollections->contains($itemCollection)) {
            $this->itemCollections->add($itemCollection);
            $itemCollection->setCategory($this);
        }

        return $this;
    }

    public function removeItemCollection(ItemCollection $itemCollection): static
    {
        if ($this->itemCollections->removeElement($itemCollection)) {
            if ($itemCollection->getCategory() === $this) {
                $itemCollection->setCategory(null);
            }
        }

        return $this;
    }
}
