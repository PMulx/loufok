<?php

namespace App\Model;

class Administrateur extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'administrateur';
    protected static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    public function getAllCadavre()
    {
        $sql = "SELECT id_cadavre, date_debut_cadavre, date_fin_cadavre
            FROM cadavre";

        $sth = self::$dbh->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }
    public function ajouterCadavre($titre, $dateDebut, $dateFin, $nbContributions, $nbJaime, $id)
    {
        $sql = "INSERT INTO cadavre (titre_cadavre, date_debut_cadavre, date_fin_cadavre, nb_contributions, nb_jaime, id_administrateur)
                VALUES (:titre, :dateDebut, :dateFin, :nbContributions, :nbJaime, :id)";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':titre', $titre);
        $sth->bindParam(':dateDebut', $dateDebut);
        $sth->bindParam(':dateFin', $dateFin);
        $sth->bindParam(':nbContributions', $nbContributions);
        $sth->bindParam(':nbJaime', $nbJaime);
        $sth->bindParam(':id', $id);
        $sth->execute();

        $idCadavre = self::$dbh->lastInsertId();

        return $idCadavre;
    }

    public function ajouterContribution($texteContribution, $idAdmin, $idCadavre)
    {
        $ordreSoumission = 1;
        $dateSoumission = date('Y-m-d');

        $sqlContribution = "INSERT INTO contribution (texte_contribution, ordre_soumission, date_soumission, id_administrateur, id_cadavre)
            VALUES (:texteContribution, :ordreSoumission, :dateSoumission, :idAdmin, :idCadavre)";

        $sthContribution = self::$dbh->prepare($sqlContribution);
        $sthContribution->bindParam(':texteContribution', $texteContribution);
        $sthContribution->bindParam(':ordreSoumission', $ordreSoumission);
        $sthContribution->bindParam(':dateSoumission', $dateSoumission);
        $sthContribution->bindParam(':idAdmin', $idAdmin);
        $sthContribution->bindParam(':idCadavre', $idCadavre);
        $sthContribution->execute();
    }
}