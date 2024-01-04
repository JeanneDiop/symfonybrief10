<?php
 
namespace App\Entity;
 
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
 
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUsers"])]
    private ?int $id = null;
 
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["getUsers"])]
    private ?string $email = null;
 
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["getUsers"])]
    private ?string $username = null;
 
    #[ORM\Column]
    private array $roles = [];
 
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(["getUsers"])]
    private ?string $password = null;

    // #[ORM\OneToMany(mappedBy: 'User', targetEntity: Formation::class)]
    // #[Groups(["getUsers"])]
    // private Collection $formations;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Candidature::class)]
    #[Groups(["getUsers"])]
    private Collection $candidatures;

   
    // #[ORM\OneToMany(mappedBy: 'User_id', targetEntity: Candidature::class)]
    // private Collection $candidatures;

    public function __construct()
    {
        // $this->formations = new ArrayCollection();
        $this->candidatures = new ArrayCollection();
       
    
    }

    // #[ORM\ManyToOne(inversedBy: 'User')]
    // private ?Formation $user = null;
 
    public function getId(): ?int
    {
        return $this->id;
    }
 
    public function getEmail(): ?string
    {
        return $this->email;
    }
 
    public function setEmail(string $email): static
    {
        $this->email = $email;
 
        return $this;
    }
 
    public function getUsername(): string
    {
        return $this->username;
    }
  
    public function setUsername(string $username): self
    {
        $this->username = $username;
  
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
        $roles[]="ROLE_USER";
        // dump();
        // die();
        return array_unique($roles);
    }
 
    public function setRoles(array $roles): static
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
 
    public function setPassword(string $password): static
    {
        $this->password = $password;
 
        return $this;
    }
 
    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // public function getUser(): ?Formation
    // {
    //     return $this->user;
    // }

    // public function setUser(?Formation $user): static
    // {
    //     $this->user = $user;

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Formation>
    //  */
    // public function getFormations(): Collection
    // {
    //     return $this->formations;
    // }

    // public function addFormation(Formation $formation): static
    // {
    //     if (!$this->formations->contains($formation)) {
    //         $this->formations->add($formation);
    //         $formation->setUser($this);
    //     }

    //     return $this;
    // }

    // public function removeFormation(Formation $formation): static
    // {
    //     if ($this->formations->removeElement($formation)) {
    //         // set the owning side to null (unless already changed)
    //         if ($formation->getUser() === $this) {
    //             $formation->setUser(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Candidature>
     */

    /**
     * @return Collection<int, Candidature>
     */

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): static
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->setUser($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): static
    {
        if ($this->candidatures->removeElement($candidature)) {
            // set the owning side to null (unless already changed)
            if ($candidature->getUser() === $this) {
                $candidature->setUser(null);
            }
        }

        return $this;
    }
   

   

   
 
}