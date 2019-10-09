<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *  @Assert\Length(
     *      min = 1,
     *      max = 50,
     *      minMessage = "Votre nom doit faire au moins {{ limit }} caractères de long",
     *      maxMessage = "Votre nom ne doit pas faire plus de {{ limit }} caractères"
     * )
     * @Assert\Regex(pattern="/^[a-z0-9_-]+$/i", message="Votre nom ne doit contenir que des lettres, des nombres, des underscores et des tirets!")
     *
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Veuillez saisir un nom")
     */
    private $nom;

    /**
     * @Assert\Length(
     *      min = 1,
     *      max = 50,
     *      minMessage = "Votre prénom doit faire au moins {{ limit }} caractères de long",
     *      maxMessage = "Votre prénom ne doit pas faire plus de {{ limit }} caractères"
     * )
     * @Assert\Regex(pattern="/^[a-z0-9_-]+$/i", message="Votre prénom ne doit contenir que des lettres, des nombres, des underscores et des tirets!")
     *
     * @ORM\Column(type="string", length=30)
     */
    private $prenom;

    /**
     *      @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      minMessage = "Votre numéro de téléphone doit faire au moins {{ limit }} caractères de long",
     *      maxMessage = "Votre numéro de téléphone ne doit pas faire plus de {{ limit }} caractères"
     * )
     * @Assert\Regex(pattern="/^[0-9]+$/i", message="Votre numéro de téléphone ne doit contenir que des nombres!")
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @Assert\NotBlank(message="Votre mail ne doit pas être vide")
     * @Assert\Email(message="Votre mail n'est pas valide!")
     *
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $mail;

    /**
     * @Assert\NotBlank(message="Votre mot de passe ne peux pas être vide")
     * @Assert\Length(
     *      min = 4,
     *      max = 4096,
     *      minMessage = "Votre mot de passe doit faire au moins {{ limit }} caractères de long",
     *      maxMessage = "Votre mot de passe doit faire moins de {{ limit }} caractères"
     * )
     *
     * @ORM\Column(type="string", length=4096)
     */
    private $mot_de_passe;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="participants")
     * @ORM\JoinColumn(name="campus", referencedColumnName="id")
     */
    private $campus;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="participant_o")
     */
    private $sorties_organisation;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sortie", mappedBy="participants_p")
     */
    private $sortie_participation;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @return mixed
     */
    public function getSortiesOrganisation()
    {
        return $this->sorties_organisation;
    }

    /**
     * @param mixed $sorties_organisation
     */
    public function setSortiesOrganisation($sorties_organisation)
    {
        $this->sorties_organisation = $sorties_organisation;
    }

    /**
     * @return mixed
     */
    public function getSortieParticipation()
    {
        return $this->sortie_participation;
    }

    /**
     * @param mixed $sortie_participation
     */
    public function setSortieParticipation($sortie_participation)
    {
        $this->sortie_participation = $sortie_participation;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus)
    {
        $this->campus = $campus;
    }

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
        //return array('ROLE_ADMIN');
    }

    public function setRoles($roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role): self
    {

        $roles = $this->roles;
        $roles[] = $role;
        $this->roles = array_unique($roles);

        return $this;
    }

    public function getSalt(){return null;}
    public function eraseCredentials(){}

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword()
    {
        return $this->mot_de_passe;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->mail;
    }
}
