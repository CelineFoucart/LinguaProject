<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $languageTranslatedName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $languageOriginalName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $languageDescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $languageAbout = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banner = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $favicon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLanguageTranslatedName(): ?string
    {
        return $this->languageTranslatedName;
    }

    public function setLanguageTranslatedName(string $languageTranslatedName): self
    {
        $this->languageTranslatedName = $languageTranslatedName;

        return $this;
    }

    public function getLanguageOriginalName(): ?string
    {
        return $this->languageOriginalName;
    }

    public function setLanguageOriginalName(?string $languageOriginalName): self
    {
        $this->languageOriginalName = $languageOriginalName;

        return $this;
    }

    public function getLanguageDescription(): ?string
    {
        return $this->languageDescription;
    }

    public function setLanguageDescription(?string $languageDescription): self
    {
        $this->languageDescription = $languageDescription;

        return $this;
    }

    public function getLanguageAbout(): ?string
    {
        return $this->languageAbout;
    }

    public function setLanguageAbout(?string $languageAbout): self
    {
        $this->languageAbout = $languageAbout;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    public function setFavicon(?string $favicon): self
    {
        $this->favicon = $favicon;

        return $this;
    }
}
