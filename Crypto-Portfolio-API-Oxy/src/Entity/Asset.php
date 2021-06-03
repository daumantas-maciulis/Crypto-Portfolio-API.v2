<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\AssetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AssetRepository::class)
 */
class Asset
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank()
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=8)
     * @Assert\Choice(choices={"BTC", "ETH", "IOTA"}, message="You can only select BTC, ETH and IOTA")
     */
    private $currency;

    /**
     * @ORM\Column(type="float")
     * @Assert\PositiveOrZero()
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="assets")
     */
    private $owner;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceInUsd;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getPriceInUsd(): ?float
    {
        return $this->priceInUsd;
    }

    public function setPriceInUsd(?float $priceInUsd): self
    {
        $this->priceInUsd = $priceInUsd;

        return $this;
    }
}
