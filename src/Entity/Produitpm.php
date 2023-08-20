<?php

namespace App\Entity;

use App\Repository\CommandpmRepository;
use App\Repository\ProduitpmRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ProduitpmRepository::class)]

class Produitpm
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"IDProd")]
    private ?int  $idprod;


    #[ORM\Column(length: 255, nullable: true,name:"NomProd")]
    private ?string $nomprod;


    #[ORM\Column(nullable: true,name:"referenceP")]

    private ?int  $referencep;


    #[ORM\Column(nullable: true,name:"quantiteP")]
    private ?int $quantitep;


    #[ORM\Column(length: 255, nullable: true,name:"typep")]
    private ?string $typep;


    #[ORM\Column(nullable: true,name:"prixPM")]
    private ?int $prixpm;


    #[ORM\Column(length: 255,nullable: true,name:"QRcode")]

    private ?string $qrcode;


    #[ORM\Column(length: 255,nullable: true,name:"dateAjoutPM")]
    private ?string $dateajoutpm;

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

    public function getReferencep(): ?int
    {
        return $this->referencep;
    }

    public function setReferencep(int $referencep): self
    {
        $this->referencep = $referencep;

        return $this;
    }

    public function getQuantitep(): ?int
    {
        return $this->quantitep;
    }

    public function setQuantitep(int $quantitep): self
    {
        $this->quantitep = $quantitep;

        return $this;
    }

    public function getTypep(): ?string
    {
        return $this->typep;
    }

    public function setTypep(?string $typep): self
    {
        $this->typep = $typep;

        return $this;
    }

    public function getPrixpm(): ?int
    {
        return $this->prixpm;
    }

    public function setPrixpm(int $prixpm): self
    {
        $this->prixpm = $prixpm;

        return $this;
    }

    public function getQrcode(): ?string
    {
        return $this->qrcode;
    }

    public function setQrcode(string $qrcode): self
    {
        $this->qrcode = $qrcode;

        return $this;
    }

    public function getDateajoutpm(): ?string
    {
        return $this->dateajoutpm;
    }

    public function setDateajoutpm(string $dateajoutpm): self
    {
        $this->dateajoutpm = $dateajoutpm;

        return $this;
    }


}
