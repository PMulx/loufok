<?php

namespace App\Model;

class JoueurAdministrateur extends Model
{
    protected $tableAdminstrateur = APP_TABLE_PREFIX.'administrateur';
    protected $tableJoueur = APP_TABLE_PREFIX.'joueur';
    protected $tableContribution = APP_TABLE_PREFIX.'contribution';
    protected $tableCadavre = APP_TABLE_PREFIX.'cadavre';
    protected static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function checkLogin($email, $password)
    {
        $sql = "SELECT id_joueur AS id, ad_mail_joueur AS email, mot_de_passe_joueur AS mot_de_passe, 'joueur' AS type
        FROM ".$this->tableJoueur."
        WHERE ad_mail_joueur = :email AND mot_de_passe_joueur = :password
        UNION
        SELECT id_administrateur AS id, ad_mail_administrateur AS email, mot_de_passe_administrateur AS mot_de_passe, 'administrateur' AS type
        FROM ".$this->tableAdminstrateur.'
        WHERE ad_mail_administrateur = :email AND mot_de_passe_administrateur = :password';

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':email', $email);
        $sth->bindParam(':password', $password);
        $sth->execute();

        return $sth->fetch();
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        session_write_close();
    }

    public function getLastCadavre($id)
    {
        // Étape 1 : Récupérer l'ID du dernier cadavre
        $sql1 = 'SELECT co.id_cadavre
            FROM '.$this->tableContribution.' co
            WHERE co.id_joueur = :id_joueur
            ORDER BY co.date_soumission DESC
            LIMIT 1';

        $sth1 = self::$dbh->prepare($sql1);
        $sth1->bindParam(':id_joueur', $id);
        $sth1->execute();

        $cadavreId = $sth1->fetchColumn();

        // Étape 2 : Récupérer les "nom_plume" et les "texte_contribution" associés à ce cadavre
        $sql2 = 'SELECT j.nom_plume, co.texte_contribution
            FROM '.$this->tableContribution.' co
            LEFT JOIN '.$this->tableJoueur.' j ON co.id_joueur = j.id_joueur
            WHERE co.id_cadavre = :cadavre_id';

        $sth2 = self::$dbh->prepare($sql2);
        $sth2->bindParam(':cadavre_id', $cadavreId);
        $sth2->execute();

        return $sth2->fetchAll();
    }

    public function insertCadavre($titre, $dateDebut, $dateFin, $nbContributions, $nbJaime, $id)
    {
        $sql = 'INSERT INTO cadavre (titre_cadavre, date_debut_cadavre, date_fin_cadavre, nb_contributions, nb_jaime, id_administrateur)
                VALUES (:titre, :dateDebut, :dateFin, :nbContributions, :nbJaime, :id)';

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

        $sqlContribution = 'INSERT INTO contribution (texte_contribution, ordre_soumission, date_soumission, id_administrateur, id_cadavre)
            VALUES (:texteContribution, :ordreSoumission, :dateSoumission, :idAdmin, :idCadavre)';

        $sthContribution = self::$dbh->prepare($sqlContribution);
        $sthContribution->bindParam(':texteContribution', $texteContribution);
        $sthContribution->bindParam(':ordreSoumission', $ordreSoumission);
        $sthContribution->bindParam(':dateSoumission', $dateSoumission);
        $sthContribution->bindParam(':idAdmin', $idAdmin);
        $sthContribution->bindParam(':idCadavre', $idCadavre);
        $sthContribution->execute();
    }
}
