<?php

namespace App\Model;

class AdministrateurModel extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'administrateur';

    protected $id_administrateur;
    protected $ad_mail_administrateur;
    protected $mot_de_passe_administrateur;

    protected static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getIdAdministrateur(): ?int
    {
        return $this->id_administrateur;
    }

    public function getMailAdministrateur(): ?string
    {
        return $this->ad_mail_administrateur;
    }

    public function getMotDePasseAdministrateur(): ?string
    {
        return $this->mot_de_passe_administrateur;
    }

    public function setIdAdministrateur(int $id_administrateur): self
    {
        $this->id_administrateur = $id_administrateur;
        return $this;
    }

    public function setMailAdministrateur(string $ad_mail_administrateur): self
    {
        $this->ad_mail_administrateur = $ad_mail_administrateur;
        return $this;
    }

    public function setMotDePasseAdministrateur(string $mot_de_passe_administrateur): self
    {
        $this->mot_de_passe_administrateur = $mot_de_passe_administrateur;
        return $this;
    }

    public function ajouterCadavre($titre, $dateDebut, $dateFin, $nbContributions, $nbJaime)
    {
        $sql = "INSERT INTO cadavre (titre_cadavre, date_debut_cadavre, date_fin_cadavre, nb_contributions, nb_jaime, id_administrateur)
                VALUES (:titre, :dateDebut, :dateFin, :nbContributions, :nbJaime, :id)";

        session_start();

        $id_administrateur = $_SESSION['user_id'];
        $this->setIdAdministrateur($id_administrateur);

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':titre', $titre);
        $sth->bindParam(':dateDebut', $dateDebut);
        $sth->bindParam(':dateFin', $dateFin);
        $sth->bindParam(':nbContributions', $nbContributions);
        $sth->bindParam(':nbJaime', $nbJaime);
        $sth->bindParam(':id', $id_administrateur);
        $sth->execute();

        $idCadavre = self::$dbh->lastInsertId();

        return $idCadavre;
    }

    public function ajouterContribution($texteContribution, $idCadavre)
    {
        $ordreSoumission = 1;
        $dateSoumission = date('Y-m-d');

        $sqlContribution = "INSERT INTO contribution (texte_contribution, ordre_soumission, date_soumission, id_administrateur, id_cadavre)
            VALUES (:texteContribution, :ordreSoumission, :dateSoumission, :idAdmin, :idCadavre)";

        session_start();

        $id_administrateur = $_SESSION['user_id'];
        $this->setIdAdministrateur($id_administrateur);

        $sthContribution = self::$dbh->prepare($sqlContribution);
        $sthContribution->bindParam(':texteContribution', $texteContribution);
        $sthContribution->bindParam(':ordreSoumission', $ordreSoumission);
        $sthContribution->bindParam(':dateSoumission', $dateSoumission);
        $sthContribution->bindParam(':idAdmin', $id_administrateur);
        $sthContribution->bindParam(':idCadavre', $idCadavre);
        $sthContribution->execute();
    }
}
