<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['country_details'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 36, unique: true)]
    #[Groups(['country_details'])]
    private string $uuid;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['country_details'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['country_details'])]
    private string $region;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['country_details'])]
    private string $subRegion;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['country_details'])]
    private string $demonym;

    #[ORM\Column(type: 'integer')]
    #[Groups(['country_details'])]
    private int $population;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['country_details'])]
    private bool $independent;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['country_details'])]
    private string $flag;

    #[ORM\OneToMany(targetEntity: Currency::class, mappedBy: 'country')]
    #[Groups(['country_details'])]
    #[MaxDepth(1)]
    private Collection $currency;

    // Getters and Setters for each field
    public function __construct()
    {
        $this->currency = new ArrayCollection();
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getRegion(): string| null
    {
        return $this->region;
    }

    public function setRegion(string| null $region): void
    {
        $this->region = $region;
    }

    public function getSubRegion(): string| null
    {
        return $this->subRegion;
    }

    public function setSubRegion(string| null $subRegion): void
    {
        $this->subRegion = $subRegion;
    }

    public function getDemonym(): string| null
    {
        return $this->demonym;
    }

    public function setDemonym(string| null $demonym): void
    {
        $this->demonym = $demonym;
    }

    public function getPopulation(): int
    {
        return $this->population;
    }

    public function setPopulation(int $population): void
    {
        $this->population = $population;
    }

    public function isIndependent(): bool
    {
        return $this->independent;
    }

    public function setIndependent(bool $independent): void
    {
        $this->independent = $independent;
    }

    public function getFlag(): string| null
    {
        return $this->flag;
    }

    public function setFlag(string| null $flag): void
    {
        $this->flag = $flag;
    }

    public function getCurrency(): Collection
    {
        return $this->currency;
    }

    public function setCurrency(Collection $currency): void
    {
        $this->currency = $currency;
    }
}
