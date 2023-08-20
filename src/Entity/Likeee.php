<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class )]
class Likeee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"Id",nullable: false)]
    private ?int $id;


    #[ORM\Column(length: 255,nullable: false)]
    private ?string $nom;


    #[ORM\Column(name:"produit",nullable: false)]
    private ?int $produit;

 

    #[ORM\Column(name:"iduser",nullable: false)]
    private ?int $iduser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getProduit(): ?int
    {
        return $this->produit;
    }

    public function setProduit(int $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function setIduser(int $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }




}
