<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ResRepository;

#[ORM\Entity(repositoryClass: ResRepository::class)]
class Res
{

    #[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private  ?int $idRes;


    #[ORM\Column(length:255 )]
    #[Assert\NotBlank(message:"Remplissez ce champ!!")]
    private  ?string $nomArtiste;


    #[ORM\Column(length:255 )]
    #[Assert\NotBlank(message:"Remplissez ce champ!!")]
    private ?string $dateRes;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Remplissez ce champ!!")]

    private  ?float $montant;

    #[ORM\Column]
    private int $Salle_id ;





    public function getIdRes(): ?int
    {
        return $this->idRes;
    }

    public function getNomArtiste(): ?string
    {
        return $this->nomArtiste;
    }

    public function setNomArtiste(string $nomArtiste): self
    {
        $this->nomArtiste = $nomArtiste;

        return $this;
    }

    public function getDateRes(): ?string
    {
        return $this->dateRes;
    }

    public function setDateRes(string $dateRes): self
    {
        $this->dateRes = $dateRes;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getNumSalle(): ?self
    {
        return $this->numSalle;
    }

    public function setNumSalle(?self $numSalle): self
    {
        $this->numSalle = $numSalle;

        return $this;
    }

//    public function getSalle(): ?Salle
//    {
//        return $this->salle;
//    }
//
//    public function setSalle(?Salle $salle): self
//    {
//        $this->salle = $salle;
//
//        return $this;
//    }

    public function getSalleId(): int
    {
        return $this->Salle_id;
    }

    public function setSalleId(int $Salle_id): self
    {
        $this->Salle_id = $Salle_id;

        return $this;
    }


}
