<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class )]
class Produit
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"IdPROD",nullable: false)]
    private ?int $idprod;


    #[Assert\NotBlank(message:'Nom Oeuvre doit etre non vide')]
    #[ORM\Column(name:"NomProd",nullable: false,length: 255)]
    private ?string $nomprod;


    #[Assert\NotBlank(message: 'Prix doit etre non vide')]
    #[ORM\Column(name:"PrixProd",nullable: false)]
    private ?float $prixprod;


    #[Assert\NotBlank(message: 'Localisation doit etre non vide')]
    #[ORM\Column(name:"LocalisationProd",nullable: false,length:255 )]
    private ?string $localisationprod;


    #[Assert\NotBlank(message: 'Type doit etre non vide')]
    #[ORM\Column(name:"TypeProd",nullable: false,length: 255)]
    private ?string $typeprod;


    #[Assert\NotBlank(message: 'Statue doit etre non vide')]
    #[ORM\Column(name:"TypeStatue",nullable: false,length: 255)]
    private ?string $typestatue;


    #[ORM\Column(name:"imagem1",nullable: false,length: 500)]
    private ?string $imagem1;


    #[ORM\Column(nullable: true)]
    private ?string $rating;

    public function __construct()
    {
        $this->rating = new ArrayCollection();
    }

    public function getIdprod(): ?int
    {
        return $this->idprod;
    }

    public function getNomprod(): ?string
    {
        return $this->nomprod;
    }

    public function setNomprod(string $nomprod): self
    {
        $this->nomprod = $nomprod;

        return $this;
    }

    public function getPrixprod(): ?float
    {
        return $this->prixprod;
    }

    public function setPrixprod(float $prixprod): self
    {
        $this->prixprod = $prixprod;

        return $this;
    }

    public function getLocalisationprod(): ?string
    {
        return $this->localisationprod;
    }

    public function setLocalisationprod(string $localisationprod): self
    {
        $this->localisationprod = $localisationprod;

        return $this;
    }

    public function getTypeprod(): ?string
    {
        return $this->typeprod;
    }

    public function setTypeprod(string $typeprod): self
    {
        $this->typeprod = $typeprod;

        return $this;
    }

    public function getTypestatue(): ?string
    {
        return $this->typestatue;
    }

    public function setTypestatue(string $typestatue): self
    {
        $this->typestatue = $typestatue;

        return $this;
    }

    public function getImagem1(): ?string
    {
        return $this->imagem1;
    }

    public function setImagem1(string $imagem1): self
    {
        $this->imagem1 = $imagem1;

        return $this;
    }


    public function setRating(string $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return Collection<int, Likeee>
     */
    public function getRating(): string
    {
        return $this->rating;
    }

//    public function addRating(Like $rating): self
//    {
//        if (!$this->rating->contains($rating)) {
//            $this->rating[] = $rating;
//            $rating->setProduit($this);
//        }
//
//        return $this;
//    }
//
//    public function removeRating(Like $rating): self
//    {
//        if ($this->rating->removeElement($rating)) {
//            // set the owning side to null (unless already changed)
//            if ($rating->getProduit() === $this) {
//                $rating->setProduit(null);
//            }
//        }
//
//        return $this;
//    }


}
