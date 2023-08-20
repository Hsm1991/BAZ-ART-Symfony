<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SalleRepository;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'num_salle')]
    private ?int $id = null;

    #[ORM\Column(length:255,nullable: true ,name: 'nom')]
    #[Assert\NotBlank(message:"Remplissez ce champ!!")]
    #[Assert\Length(
        min: 6,
        max: 30,
        minMessage: 'Nom de la salle doit contenir au  minimun {{ limit }} Caractères',
        maxMessage: 'Nom de la salle doit contenir au maximum {{ limit }} Caractères',
    )]

    private ?string $nom = null;

    #[ORM\Column(length:255,nullable: true ,name:'type')]
    #[Assert\NotBlank(message:"Remplissez ce champ!!")]

//nheb nbadlou combo

    private ?string $type=null;

    #[ORM\Column(length:255, nullable: true)]
    #[Assert\NotBlank(message:"Remplissez ce champ!!")]
    private ?string $statu= null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message:"Remplissez ce champ!!")]
    #[Assert\Positive(message:"prix doit etre positif")]
    private ?float $prix= null;




    #[ORM\Column(nullable: true)]
    private ?int $capacite = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $image = null;






    public function __construct()
    {

        $this->salle = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatu(): ?string
    {
        return $this->statu;
    }

    public function setStatu(string $statu): self
    {
        $this->statu = $statu;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

//    /**
//     * @return Collection<int, Res>
//     */
//    public function getReservations(): Collection
//    {
//        return $this->reservations;
//    }
//
//    public function addReservation(Res $reservation): self
//    {
//        if (!$this->reservations->contains($reservation)) {
//            $this->reservations->add($reservation);
//            $reservation->setSalle($this);
//        }
//
//        return $this;
//    }
//
//    public function removeReservation(Res $reservation): self
//    {
//        if ($this->reservations->removeElement($reservation)) {
//            // set the owning side to null (unless already changed)
//            if ($reservation->getSalle() === $this) {
//                $reservation->setSalle(null);
//            }
//        }
//
//        return $this;
//    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(?int $capacite): self
    {
        $this->capacite = $capacite;

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

//    /**
//     * @return Collection<int, Res>
//     */
//    public function getSalleId(): Collection
//    {
//        return $this->salle_id;
//    }

//    public function addSalleId(Res $salleId): self
//    {
//        if (!$this->salle_id->contains($salleId)) {
//            $this->salle_id->add($salleId);
//            $salleId->setSalleId($this);
//        }
//
//        return $this;
//    }
//
//    public function removeSalleId(Res $salleId): self
//    {
//        if ($this->salle_id->removeElement($salleId)) {
//            // set the owning side to null (unless already changed)
//            if ($salleId->getSalleId() === $this) {
//                $salleId->setSalleId(null);
//            }
//        }
//
//        return $this;
//    }
//
//    /**
//     * @return Collection<int, Res>
//     */
//    public function getSalle(): Collection
//    {
//        return $this->salle;
//    }
//
//    public function addSalle(Res $salle): self
//    {
//        if (!$this->salle->contains($salle)) {
//            $this->salle->add($salle);
//            $salle->setSalleId($this);
//        }
//
//        return $this;
//    }
//
//    public function removeSalle(Res $salle): self
//    {
//        if ($this->salle->removeElement($salle)) {
//            // set the owning side to null (unless already changed)
//            if ($salle->getSalleId() === $this) {
//                $salle->setSalleId(null);
//            }
//        }
//
//        return $this;
//    }
//
//    public function __toString()
//    {
//        return $this->getNom();
//    }
}
