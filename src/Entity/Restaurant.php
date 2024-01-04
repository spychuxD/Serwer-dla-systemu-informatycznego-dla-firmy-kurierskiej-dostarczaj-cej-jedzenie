<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $phoneNumber = null;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: OpeningHoursRestaurant::class)]
    private Collection $restaurantOpeningHours;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $restaurantAddress = null;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: Dish::class)]
    private Collection $dishes;

    #[ORM\OneToMany(mappedBy: 'restaurant', targetEntity: RestaurantCategory::class)]
    private Collection $restaurantCategories;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?UserData $owner = null;

    #[ORM\Column(nullable: true)]
    private ?float $lat = null;

    #[ORM\Column(nullable: true)]
    private ?float $lng = null;

    public function __construct()
    {
        $this->restaurantOpeningHours = new ArrayCollection();
        $this->dishes = new ArrayCollection();
        $this->restaurantCategories = new ArrayCollection();
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

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection<int, OpeningHoursRestaurant>
     */
    public function getRestaurantOpeningHours(): Collection
    {
        return $this->restaurantOpeningHours;
    }

    public function addRestaurantOpeningHour(OpeningHoursRestaurant $restaurantOpeningHour): static
    {
        if (!$this->restaurantOpeningHours->contains($restaurantOpeningHour)) {
            $this->restaurantOpeningHours->add($restaurantOpeningHour);
            $restaurantOpeningHour->setRestaurant($this);
        }

        return $this;
    }

    public function removeRestaurantOpeningHour(OpeningHoursRestaurant $restaurantOpeningHour): static
    {
        if ($this->restaurantOpeningHours->removeElement($restaurantOpeningHour)) {
            // set the owning side to null (unless already changed)
            if ($restaurantOpeningHour->getRestaurant() === $this) {
                $restaurantOpeningHour->setRestaurant(null);
            }
        }

        return $this;
    }

    public function getRestaurantAddress(): ?Address
    {
        return $this->restaurantAddress;
    }

    public function setRestaurantAddress(Address $restaurantAddress): static
    {
        $this->restaurantAddress = $restaurantAddress;

        return $this;
    }

    /**
     * @return Collection<int, Dish>
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(Dish $dish): static
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes->add($dish);
            $dish->setRestaurant($this);
        }

        return $this;
    }

    public function removeDish(Dish $dish): static
    {
        if ($this->dishes->removeElement($dish)) {
            // set the owning side to null (unless already changed)
            if ($dish->getRestaurant() === $this) {
                $dish->setRestaurant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RestaurantCategory>
     */
    public function getRestaurantCategories(): Collection
    {
        return $this->restaurantCategories;
    }

    public function addRestaurantCategory(RestaurantCategory $restaurantCategory): static
    {
        if (!$this->restaurantCategories->contains($restaurantCategory)) {
            $this->restaurantCategories->add($restaurantCategory);
            $restaurantCategory->setRestaurant($this);
        }

        return $this;
    }

    public function removeRestaurantCategory(RestaurantCategory $restaurantCategory): static
    {
        if ($this->restaurantCategories->removeElement($restaurantCategory)) {
            // set the owning side to null (unless already changed)
            if ($restaurantCategory->getRestaurant() === $this) {
                $restaurantCategory->setRestaurant(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?UserData
    {
        return $this->owner;
    }

    public function setOwner(UserData $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): static
    {
        $this->lng = $lng;

        return $this;
    }
}
