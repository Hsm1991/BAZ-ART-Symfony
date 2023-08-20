<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'please enter valid Email ')]
    #[Assert\Email(message: 'please enter valid Email ')]
    private ?string $email = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'please enter valid Last name ')]
    #[Assert\Length(min: 3 ,max: 200,minMessage: 'Too short ',maxMessage: 'too long')]
    private ?string  $lastname;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'please enter valid Last name ')]
    #[Assert\Length(min: 3 ,max: 200,minMessage: 'Too short ',maxMessage: 'too long')]
    private ?string  $firstname;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'please enter valid Last name ')]
    #[Assert\Length(min: 3 ,minMessage: 'Too short ')]
    private ?string  $address;

    #[ORM\Column(type: 'string',length: 10)]
    #[Assert\NotBlank(message: 'please enter valid phone  ')]
    #[Assert\Length(min: 3 ,max: 15,minMessage: 'Too short ',maxMessage: 'too long')]
//    #[Assert\Type(TelType::class,message: 'enter valid phone number')]
    private ?string  $tel;


    #[ORM\Column(type: 'string', length: 100,nullable: true)]
    private ?string$resetToken ;


    #[ORM\Column(type: 'string', length: 10,nullable: true, options: ['default' => 'Actif'])]
    private ?String $status;

    /**
     * @return bool|null
     */
    public function getStatus(): ?String
    {
        return $this->status;
    }

    /**
     * @param bool|null $status
     */
    public function setStatus(?String $status): void
    {
        $this->status = $status;
    }

    public function BlockUser(?User $user): void
    {
        $this->setStatus('Blocked');
    }


    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getTel(): ?string
    {
        return $this->tel;
    }

    /**
     * @param string|null $tel
     */
    public function setTel(?string $tel): void
    {
        $this->tel = $tel;
    }



    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    #[ORM\Column(type: 'string',options: ['default'=>'Tunis'])]
    #[Assert\NotBlank(message: 'please enter valid Last name ')]
    private ?string  $city;

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean',options: ['default'=>'1'])]
    private bool $isVerified = false;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Category $category = null;

    #[ORM\Column (type: 'datetime_immutable')]
    private ?\DateTimeImmutable $created_at = null ;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->created_at = $createdAt;

        return $this;
    }

    public function getSalt(){

    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }
}

