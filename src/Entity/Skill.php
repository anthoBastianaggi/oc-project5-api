<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SkillRepository::class)
 */
class Skill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list_skill"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list_skill"})
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     * @Assert\Type("string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=3)
     * @Groups({"list_skill"})
     * @Assert\NotBlank
     * @Assert\Type("numeric")
     */
    private $percentage;

    /**
     * @ORM\ManyToOne(targetEntity=CategorySkills::class, inversedBy="skills")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"cat_skill"})
     * @Assert\NotBlank
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

    public function getPercentage(): ?string
    {
        return $this->percentage;
    }

    public function setPercentage(string $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getCategory(): ?CategorySkills
    {
        return $this->category;
    }

    public function setCategory(?CategorySkills $category): self
    {
        $this->category = $category;

        return $this;
    }
}
