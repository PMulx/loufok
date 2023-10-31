<?php

namespace App\Model;

class CadavreModel extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'cadavre';

    protected $id_cadavre;
    protected $titre_cadavre;
    protected $date_debut_cadavre;
    protected $date_fin_cadavre;
    protected $nb_contributions;
    protected $nb_jaime;
    protected $id_administrateur;

    protected static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getIdCadavre(): ?int
    {
        return $this->id_cadavre;
    }

    public function getTitreCadavre(): ?string
    {
        return $this->titre_cadavre;
    }

    public function getDateDebutCadavre(): ?string
    {
        return $this->date_debut_cadavre;
    }

    public function getDateFinCadavre(): ?string
    {
        return $this->date_fin_cadavre;
    }

    public function getNbContributions(): ?int
    {
        return $this->nb_contributions;
    }

    public function getNbJaime(): ?int
    {
        return $this->nb_jaime;
    }

    public function getIdAdministrateur(): ?int
    {
        return $this->id_administrateur;
    }

    public function setIdCadavre(int $id_cadavre): self
    {
        $this->id_cadavre = $id_cadavre;
        return $this;
    }

    public function setTitreCadavre(string $titre_cadavre): self
    {
        $this->titre_cadavre = $titre_cadavre;
        return $this;
    }

    public function setDateDebutCadavre(string $date_debut_cadavre): self
    {
        $this->date_debut_cadavre = $date_debut_cadavre;
        return $this;
    }

    public function setDateFinCadavre(string $date_fin_cadavre): self
    {
        $this->date_fin_cadavre = $date_fin_cadavre;
        return $this;
    }

    public function setNbContributions(int $nb_contributions): self
    {
        $this->nb_contributions = $nb_contributions;
        return $this;
    }

    public function setNbJaime(int $nb_jaime): self
    {
        $this->nb_jaime = $nb_jaime;
        return $this;
    }

    public function setIdAdministrateur(int $id_administrateur): self
    {
        $this->id_administrateur = $id_administrateur;
        return $this;
    }

    public function getPeriodes()
    {
        $sql = "SELECT id_cadavre, CONCAT(date_debut_cadavre, ' | ', date_fin_cadavre) AS periode
                FROM cadavre";

        $sth = self::$dbh->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }
}
