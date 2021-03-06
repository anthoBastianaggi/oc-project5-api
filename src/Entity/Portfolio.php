<?php

namespace App\Entity;

use App\Repository\PortfolioRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PortfolioRepository::class)
 */
class Portfolio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list_portfolio"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list_portfolio"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups({"list_portfolio"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list_portfolio"})
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list_portfolio"})
     */
    private $link;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"list_portfolio"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryPortfolio::class, inversedBy="portfolio")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cat_portfolio"})
     */
    private $category;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategory(): ?CategoryPortfolio
    {
        return $this->category;
    }

    public function setCategory(?CategoryPortfolio $category): self
    {
        $this->category = $category;

        return $this;
    }
}
