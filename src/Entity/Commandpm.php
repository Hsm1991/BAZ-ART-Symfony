<?php

namespace App\Entity;

use App\Repository\CommandpmRepository;
use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: CommandpmRepository::class)]
class Commandpm
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"idCPM")]
    private ?int $idcpm;

    #[ORM\Column( nullable: true,name:"IDProd")]
    private?int  $idprod;

    #[ORM\Column(length: 255, nullable: true , name: 'NomProd')]
    private ?string $nomprod;


    #[ORM\Column( nullable: true,name:"referenceCM")]
    private ?int $referencecm;


    #[ORM\Column(length: 255, nullable: true , name: 'date')]

    private ?string $date;


    #[ORM\Column( nullable: true,name:"quantiteCpm")]
    private $quantitecpm;


    #[ORM\Column( nullable: true,name:"iduser")]
    private ?int  $iduser;


    #[ORM\Column(length: 255, nullable: true , name: 'type')]
    private ?string $type;

    public function getIdcpm(): ?int
    {
        return $this->idcpm;
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

    public function getNomprod(): ?string
    {
        return $this->nomprod;
    }

    public function setNomprod(string $nomprod): self
    {
        $this->nomprod = $nomprod;

        return $this;
    }

    public function getReferencecm(): ?int
    {
        return $this->referencecm;
    }

    public function setReferencecm(int $referencecm): self
    {
        $this->referencecm = $referencecm;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getQuantitecpm(): ?int
    {
        return $this->quantitecpm;
    }

    public function setQuantitecpm(int $quantitecpm): self
    {
        $this->quantitecpm = $quantitecpm;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }


}
