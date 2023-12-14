<?php

namespace App\Entity;

use App\Repository\OpeningHoursRestaurantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OpeningHoursRestaurantRepository::class)]
class OpeningHoursRestaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $dayOfWeekFrom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $openHour = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $closeHour = null;

    #[ORM\ManyToOne(inversedBy: 'restaurantOpeningHours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    #[ORM\Column(length: 15)]
    private ?string $dayOfWeekTo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeekFrom(): ?string
    {
        return $this->dayOfWeekFrom;
    }

    public function setDayOfWeekFrom(string $dayOfWeekFrom): static
    {
        $this->dayOfWeekFrom = $dayOfWeekFrom;

        return $this;
    }

    public function getOpenHour(): ?\DateTimeInterface
    {
        return $this->openHour;
    }

    public function setOpenHour(\DateTimeInterface $openHour): static
    {
        $this->openHour = $openHour;

        return $this;
    }

    public function getCloseHour(): ?\DateTimeInterface
    {
        return $this->closeHour;
    }

    public function setCloseHour(\DateTimeInterface $closeHour): static
    {
        $this->closeHour = $closeHour;

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

    public function getDayOfWeekTo(): ?string
    {
        return $this->dayOfWeekTo;
    }

    public function setDayOfWeekTo(string $dayOfWeekTo): static
    {
        $this->dayOfWeekTo = $dayOfWeekTo;

        return $this;
    }
}
