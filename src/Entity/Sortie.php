<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_cloture;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_inscription_max;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $infos_sortie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus", inversedBy="sorties")
     * @ORM\JoinColumn(name="campus", referencedColumnName="id")
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant", inversedBy="sorties_organisation")
     * @ORM\JoinColumn(name="orgarnisateur", referencedColumnName="id")
     */
    private $participant_o;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant", inversedBy="sortie_participation")
     * @ORM\JoinTable(name="sortie_participant")
     */
    private $participants_p;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat", inversedBy="sorties")
     * @ORM\JoinColumn(name="etat", referencedColumnName="id")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="sorties")
     * @ORM\JoinColumn("lieu", referencedColumnName="id")
     */
    private $lieu;

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getParticipantO()
    {
        return $this->participant_o;
    }

    /**
     * @param mixed $participant_o
     */
    public function setParticipantO($participant_o)
    {
        $this->participant_o = $participant_o;
    }

    /**
     * @return mixed
     */
    public function getParticipantsP()
    {
        return $this->participants_p;
    }

    /**
     * @param mixed $participants_p
     */
    public function setParticipantsP($participants_p)
    {
        $this->participants_p = $participants_p;
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->date_cloture;
    }

    public function setDateCloture(\DateTimeInterface $date_cloture): self
    {
        $this->date_cloture = $date_cloture;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nb_inscription_max;
    }

    public function setNbInscriptionMax(int $nb_inscription_max): self
    {
        $this->nb_inscription_max = $nb_inscription_max;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infos_sortie;
    }

    public function setInfosSortie(?string $infos_sortie): self
    {
        $this->infos_sortie = $infos_sortie;

        return $this;
    }
}
