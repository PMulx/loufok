<?php

namespace App\Model;

class Joueur extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'joueur';

    protected static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    public function getContributionCount()
    {
        $sql = "SELECT c.id_cadavre, c.date_debut_cadavre, c.date_fin_cadavre, COUNT(co.id_contribution) AS contribution_count
            FROM cadavre c
            LEFT JOIN contribution co ON c.id_cadavre = co.id_cadavre
            GROUP BY c.id_cadavre, c.date_debut_cadavre, c.date_fin_cadavre";

        $sth = self::$dbh->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }
    public function ajouterAleatoire($nbAleatoire, $id, $idCadavre)
    {
        $sqlCheck = "SELECT 1 FROM contribution_aléatoire WHERE id_joueur = :idJoueur AND id_cadavre = :idCadavre LIMIT 1";

        $sthCheck = self::$dbh->prepare($sqlCheck);
        $sthCheck->bindParam(':idJoueur', $id);
        $sthCheck->bindParam(':idCadavre', $idCadavre);
        $sthCheck->execute();

        $row = $sthCheck->fetch();
        if (!$row) {
            $sql = "INSERT INTO contribution_aléatoire (num_contribution, id_joueur, id_cadavre)
                    VALUES (:numContribution, :idJoueur, :idCadavre)";

            $sth = self::$dbh->prepare($sql);
            $sth->bindParam(':numContribution', $nbAleatoire);
            $sth->bindParam(':idJoueur', $id);
            $sth->bindParam(':idCadavre', $idCadavre);
            $sth->execute();
        }
    }

    public function getCadavreInfo($idcadavre)
    {
        $sql = "SELECT cadavre.id_cadavre, MAX(cadavre.nb_contributions) AS nb_contribution_max, contribution.ordre_soumission
                FROM cadavre
                INNER JOIN contribution ON cadavre.id_cadavre = contribution.id_cadavre
                WHERE cadavre.id_cadavre = :id_cadavre
                GROUP BY cadavre.id_cadavre,  contribution.ordre_soumission";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':id_cadavre', $idcadavre);
        $sth->execute();

        return $sth->fetchAll();
    }
    public function getContributionAleatoireTexte($id, $idcadavre)
    {
        $sql = "SELECT ca.num_contribution, co.texte_contribution
            FROM contribution_aléatoire AS ca
            INNER JOIN contribution AS co ON ca.id_cadavre = co.id_cadavre AND ca.num_contribution = co.ordre_soumission
            WHERE ca.id_joueur = :id AND ca.id_cadavre = :id_cadavre";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':id', $id);
        $sth->bindParam(':id_cadavre', $idcadavre);
        $sth->execute();

        return $sth->fetchAll();
    }
    public function insererContribution($texteContribution, $ordreSoumission, $dateSoumission, $idcadavre, $id)
    {
        $sql = "INSERT INTO contribution (texte_contribution, ordre_soumission, date_soumission, id_cadavre, id_joueur)
            VALUES (:texteContribution, :ordreSoumission, :dateSoumission, :idCadavre, :idJoueur)";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':texteContribution', $texteContribution);
        $sth->bindParam(':ordreSoumission', $ordreSoumission);
        $sth->bindParam(':dateSoumission', $dateSoumission);
        $sth->bindParam(':idCadavre', $idcadavre);
        $sth->bindParam(':idJoueur', $id);
        $sth->execute();
    }
    public function getContributionByIds($id, $idcadavre)
    {
        $sql = "SELECT ordre_soumission, texte_contribution
            FROM contribution
            WHERE id_joueur = :idJoueur AND id_cadavre = :idCadavre";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':idJoueur', $id);
        $sth->bindParam(':idCadavre', $idcadavre);
        $sth->execute();

        return $sth->fetch();
    }
}
