<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
/**
 * @Vich\Uploadable
 */
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ ne peut être vide.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Ce champ doit faire au moins 2 caractères.",
        maxMessage: "Ce champ ne peut pas faire plus de 255 caractères."
    )]
    private ?string $languageTranslatedName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Ce champ doit faire au moins 2 caractères.",
        maxMessage: "Ce champ ne peut pas faire plus de 255 caractères."
    )]
    private ?string $languageOriginalName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Ce champ doit faire au moins 2 caractères.",
        maxMessage: "Ce champ ne peut pas faire plus de 255 caractères."
    )]
    private ?string $languageDescription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $languageAbout = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banner = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $favicon = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @Vich\UploadableField(mapping="settings_banner", fileNameProperty="banner")
     * @var File
     */
    private $bannerFile;

    /**
     * @Vich\UploadableField(mapping="settings_favicon", fileNameProperty="favicon")
     * @var File
     */
    private $faviconFile;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of favisonFile
     */
    public function getFaviconFile()
    {
        return $this->faviconFile;
    }

    /**
     * Set the value of favisonFile
     */
    public function setFaviconFile(?File $faviconFile = null): self
    {
        $this->faviconFile = $faviconFile;

        if ($faviconFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * Get the value of bannerFile
     */
    public function getBannerFile()
    {
        return $this->bannerFile;
    }

    /**
     * Set the value of bannerFile
     */
    public function setBannerFile(?File $bannerFile = null): self
    {
        $this->bannerFile = $bannerFile;

        if ($bannerFile) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }
}
