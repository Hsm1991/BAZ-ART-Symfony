<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $iduser = null;

    #[Assert\NotBlank(message: "veillez entrer une description")]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $recdate = null;

    #[ORM\Column(length: 255)]
    private ?string $selon = null;

    #[Assert\NotBlank(message: "veillez entrez un email")]
    #[Assert\Email(message: "veillez entrez un email de forme valide")]
    #[ORM\Column(length: 255)]
    private ?string $mail = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRecdate(): ?string
    {
        return $this->recdate;
    }

    public function setRecdate(string $recdate): self
    {
        $this->recdate = $recdate;

        return $this;
    }

    public function getSelon(): ?string
    {
        return $this->selon;
    }

    public function setSelon(string $selon): self
    {
        $this->selon = $selon;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function setIduser(string $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }
}
