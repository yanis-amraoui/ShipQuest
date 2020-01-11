<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email est deja utilisée"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8",minMessage="Votre mot de passee doit faire minimum 8 caracteres")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="vos message ne correspondent pas")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="integer")
     */
    private $Coins;

    /**
     * @ORM\Column(type="integer")
     */
    private $Level;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", inversedBy="UserProduct")
     */
    private $Achat;

    /**
     * @ORM\Column(type="integer")
     */
    private $backgroundActive;

    /**
     * @ORM\Column(type="integer")
     */
    private $skinActive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $accountActive;

    /**
     * @ORM\Column(type="integer")
     */
    private $activeLoadBackground;

    /**
     * @ORM\Column(type="integer")
     */
    private $xp;

    public function __construct()
    {
        $this->Achat = new ArrayCollection();
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    public function getRoles()
    {
       return['ROLE_USER'];
    }
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getCoins(): ?int
    {
        return $this->Coins;
    }

    public function setCoins(int $Coins): self
    {
        $this->Coins = $Coins;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->Level;
    }

    public function setLevel(int $Level): self
    {
        $this->Level = $Level;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getAchat(): Collection
    {
        return $this->Achat;
    }

    public function addAchat(Product $achat): self
    {
        if (!$this->Achat->contains($achat)) {
            $this->Achat[] = $achat;
        }

        return $this;
    }

    public function removeAchat(Product $achat): self
    {
        if ($this->Achat->contains($achat)) {
            $this->Achat->removeElement($achat);
        }

        return $this;
    }

    public function getBackgroundActive(): ?int
    {
        return $this->backgroundActive;
    }

    public function setBackgroundActive(int $backgroundActive): self
    {
        $this->backgroundActive = $backgroundActive;

        return $this;
    }

    public function getSkinActive(): ?int
    {
        return $this->skinActive;
    }

    public function setSkinActive(int $skinActive): self
    {
        $this->skinActive = $skinActive;

        return $this;
    }

    public function getAccountActive(): ?bool
    {
        return $this->accountActive;
    }

    public function setAccountActive(bool $accountActive): self
    {
        $this->accountActive = $accountActive;

        return $this;
    }

    public function getActiveLoadBackground(): ?int
    {
        return $this->activeLoadBackground;
    }

    public function setActiveLoadBackground(int $activeLoadBackground): self
    {
        $this->activeLoadBackground = $activeLoadBackground;

        return $this;
    }

    public function getXp(): ?int
    {
        return $this->xp;
    }

    public function setXp(int $xp): self
    {
        $this->xp = $xp;

        return $this;
    }
}
