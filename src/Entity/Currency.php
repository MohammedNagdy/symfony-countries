<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['currency_details', 'country_details'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 4, unique: true)]
    #[Groups(['currency_details', 'country_details'])]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['currency_details', 'country_details'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(['currency_details', 'country_details'])]
    private string $symbol;

    #[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'currencies',  cascade: ['remove'])]
    #[Groups(['currency_details'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Country $country = null;

    // Getters and Setters for each field
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }
}
