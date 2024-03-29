<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\OneToMany(targetEntity=Address::class, mappedBy="user")
     */
    private $SeveralAddress;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    private $SeveralOrders;

    public function __construct()
    {
        $this->SeveralAddress = new ArrayCollection();
        $this->SeveralOrders = new ArrayCollection();
    }

    public function getFullName(): string{
        return $this->getFirstname().' '. $this->getLastname();
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
    public function getUsername(): string
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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getSeveralAddress(): Collection
    {
        return $this->SeveralAddress;
    }

    public function addSeveralAddress(Address $severalAddress): self
    {
        if (!$this->SeveralAddress->contains($severalAddress)) {
            $this->SeveralAddress[] = $severalAddress;
            $severalAddress->setUser($this);
        }

        return $this;
    }

    public function removeSeveralAddress(Address $severalAddress): self
    {
        if ($this->SeveralAddress->removeElement($severalAddress)) {
            // set the owning side to null (unless already changed)
            if ($severalAddress->getUser() === $this) {
                $severalAddress->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getSeveralOrders(): Collection
    {
        return $this->SeveralOrders;
    }

    public function addSeveralOrder(Order $severalOrder): self
    {
        if (!$this->SeveralOrders->contains($severalOrder)) {
            $this->SeveralOrders[] = $severalOrder;
            $severalOrder->setUser($this);
        }

        return $this;
    }

    public function removeSeveralOrder(Order $severalOrder): self
    {
        if ($this->SeveralOrders->removeElement($severalOrder)) {
            // set the owning side to null (unless already changed)
            if ($severalOrder->getUser() === $this) {
                $severalOrder->setUser(null);
            }
        }

        return $this;
    }
}
