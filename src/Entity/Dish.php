<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DishRepository::class)]
class Dish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'dishes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    #[ORM\ManyToOne(inversedBy: 'dishes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DishCategory $dishCategory = null;

    #[ORM\OneToMany(mappedBy: 'dish', targetEntity: DishIngridient::class)]
    private Collection $dishIngridients;

    public function __construct()
    {
        $this->dishIngridients = new ArrayCollection();
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getDishCategory(): ?DishCategory
    {
        return $this->dishCategory;
    }

    public function setDishCategory(?DishCategory $dishCategory): static
    {
        $this->dishCategory = $dishCategory;

        return $this;
    }

    /**
     * @return Collection<int, DishIngridient>
     */
    public function getDishIngridients(): Collection
    {
        return $this->dishIngridients;
    }

    public function addDishIngridient(DishIngridient $dishIngridient): static
    {
        if (!$this->dishIngridients->contains($dishIngridient)) {
            $this->dishIngridients->add($dishIngridient);
            $dishIngridient->setDish($this);
        }

        return $this;
    }

    public function removeDishIngridient(DishIngridient $dishIngridient): static
    {
        if ($this->dishIngridients->removeElement($dishIngridient)) {
            // set the owning side to null (unless already changed)
            if ($dishIngridient->getDish() === $this) {
                $dishIngridient->setDish(null);
            }
        }

        return $this;
    }
}
