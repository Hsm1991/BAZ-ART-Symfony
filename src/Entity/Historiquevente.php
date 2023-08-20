<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use App\Repository\HistoriqueventeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueventeRepository::class )]
class Historiquevente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"IdVent",nullable: true)]
    private ?int $idvent;


    #[ORM\Column(name:"DateVent",nullable: true,length: 255)]
    private ?string $datevent;


    #[ORM\Column(name:"QteVendue",nullable: true)]
    private ?float $qtevendue;


    #[ORM\Column(name:"PrixVente",nullable: true)]
    private ?float $prixvente;


    #[ORM\Column(name:"IdPROD",nullable: true)]
    private ?int $idprod;

    public function getIdvent(): ?int
    {
        return $this->idvent;
    }

    public function getDatevent(): ?string
    {
        return $this->datevent;
    }

    public function setDatevent(string $datevent): self
    {
        $this->datevent = $datevent;

        return $this;
    }

    public function getQtevendue(): ?float
    {
        return $this->qtevendue;
    }

    public function setQtevendue(float $qtevendue): self
    {
        $this->qtevendue = $qtevendue;

        return $this;
    }

    public function getPrixvente(): ?float
    {
        return $this->prixvente;
    }

    public function setPrixvente(float $prixvente): self
    {
        $this->prixvente = $prixvente;

        return $this;
    }

    public function getIdprod(): ?int
    {
        return $this->idprod;
    }

    public function setIdprod(int $idprod): self
    {
        $this->idprod = $idprod;

        return $this;
    }


}
