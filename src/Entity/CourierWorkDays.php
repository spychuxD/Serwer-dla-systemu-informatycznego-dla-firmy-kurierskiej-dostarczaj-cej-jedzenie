<?php

namespace App\Entity;

use App\Repository\CourierWorkDaysRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourierWorkDaysRepository::class)]
class CourierWorkDays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Courier $courier = null;

    #[ORM\Column(length: 255)]
    private ?string $day = null;

    #[ORM\Column(length: 5)]
    private ?string $hourFrom = null;

    #[ORM\Column(length: 5)]
    private ?string $hourTo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourier(): ?Courier
    {
        return $this->courier;
    }

    public function setCourier(?Courier $courier): static
    {
        $this->courier = $courier;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getHourFrom(): ?string
    {
        return $this->hourFrom;
    }

    public function setHourFrom(string $hourFrom): static
    {
        $this->hourFrom = $hourFrom;

        return $this;
    }

    public function getHourTo(): ?string
    {
        return $this->hourTo;
    }

    public function setHourTo(string $hourTo): static
    {
        $this->hourTo = $hourTo;

        return $this;
    }
}
