<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?int $alertThreshold = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $supplier = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $scent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'product', orphanRemoval: true)]
    private Collection $movements;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $entryDate = null;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
        // valeur automatique pour éviter le NULL sur added_at
        $this->addedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }

    public function getAlertThreshold(): ?int
    {
        return $this->alertThreshold;
    }

    public function setAlertThreshold(int $alertThreshold): static
    {
        $this->alertThreshold = $alertThreshold;
        return $this;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;
        return $this;
    }

    public function getSupplier(): ?string
    {
        return $this->supplier;
    }

    public function setSupplier(string $supplier): static
    {
        $this->supplier = $supplier;
        return $this;
    }

    public function getScent(): ?string
    {
        return $this->scent;
    }

    public function setScent(?string $scent): static
    {
        $this->scent = $scent;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getEntryDate(): ?\DateTimeImmutable
    {
        return $this->entryDate;
    }

    public function setEntryDate(?\DateTimeImmutable $entryDate): static
    {
        $this->entryDate = $entryDate;
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
     * @return Collection<int, Movement>
     */
    public function getMovements(): Collection
    {
        return $this->movements;
    }

    public function addMovement(Movement $movement): static
    {
        if (!$this->movements->contains($movement)) {
            $this->movements->add($movement);
            $movement->setProduct($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            if ($movement->getProduct() === $this) {
                $movement->setProduct(null);
            }
        }

        return $this;
    }
}
