<?php

namespace App\Entity;

use App\Repository\CategoryPortfolioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryPortfolioRepository::class)
 */
class CategoryPortfolio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"cat_portfolio"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cat_portfolio"})
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     * @Assert\Type("string")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Portfolio::class, mappedBy="category")
     */
    private $portfolio;

    public function __construct()
    {
        $this->portfolio = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Portfolio[]
     */
    public function getPortfolio(): Collection
    {
        return $this->portfolio;
    }

    public function addPortfolio(Portfolio $portfolio): self
    {
        if (!$this->portfolio->contains($portfolio)) {
            $this->portfolio[] = $portfolio;
            $portfolio->setCategory($this);
        }

        return $this;
    }

    public function removePortfolio(Portfolio $portfolio): self
    {
        if ($this->portfolio->contains($portfolio)) {
            $this->portfolio->removeElement($portfolio);
            // set the owning side to null (unless already changed)
            if ($portfolio->getCategory() === $this) {
                $portfolio->setCategory(null);
            }
        }

        return $this;
    }
}
