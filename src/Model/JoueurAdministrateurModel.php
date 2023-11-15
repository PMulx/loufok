<?php

namespace App\Model;

class JoueurAdministrateurModel extends Model
{
    protected $tableAdminstrateur = APP_TABLE_PREFIX . 'administrateur';
    protected $tableJoueur = APP_TABLE_PREFIX . 'joueur';
    protected $tableContribution = APP_TABLE_PREFIX . 'contribution';
    protected $tableCadavre = APP_TABLE_PREFIX . 'cadavre';
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
        FROM {$this->tableJoueur}
        WHERE ad_mail_joueur = :email AND mot_de_passe_joueur = :password
        UNION
        SELECT id_administrateur AS id, ad_mail_administrateur AS email, mot_de_passe_administrateur AS mot_de_passe, 'administrateur' AS type
        FROM {$this->tableAdminstrateur}
        WHERE ad_mail_administrateur = :email AND mot_de_passe_administrateur = :password";

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

    public function getLastFinishedCadavre($id)
    {
        // Sélectionner le dernier cadavre terminé
        $sql = "SELECT c.id_cadavre
            FROM {$this->tableCadavre} c
            JOIN {$this->tableContribution} co ON c.id_cadavre = co.id_cadavre
            WHERE (c.date_fin_cadavre < CURDATE() OR c.nb_contributions <= (SELECT COUNT(ordre_soumission) FROM {$this->tableContribution} WHERE id_cadavre = c.id_cadavre))
            AND co.id_joueur = :id_joueur
            ORDER BY c.date_fin_cadavre DESC
            LIMIT 1";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':id_joueur', $id);
        $sth->execute();

        $id_cadavre = $sth->fetchColumn();

        if ($id_cadavre) {
            return $id_cadavre;
        } else {
            return null; // Aucun cadavre trouvé pour cet utilisateur
        }
    }

    public function getCompleteCadavreInfo($id)
    {
        // Utilisez la méthode getLastFinishedCadavre pour obtenir l'id_cadavre
        $id_cadavre = $this->getLastFinishedCadavre($id);

        // Si vous obtenez un id_cadavre valide, vous pouvez récupérer les informations souhaitées
        if ($id_cadavre) {
            $sql = "SELECT c.*, co.*, IFNULL(j.nom_plume, 'Administrateur') AS nom_plume
            FROM {$this->tableCadavre} c
            JOIN {$this->tableContribution} co ON c.id_cadavre = co.id_cadavre
            LEFT JOIN {$this->tableJoueur} j ON co.id_joueur = j.id_joueur
            WHERE c.id_cadavre = :id_cadavre";

            $sth = self::$dbh->prepare($sql);
            $sth->bindParam(':id_cadavre', $id_cadavre);
            $sth->execute();

            // Vous obtiendrez un ensemble de résultats avec toutes les informations souhaitées
            return $sth->fetchAll();
        } else {
            // Traitez le cas où l'id_cadavre est nul ou inexistant
            return null;
        }
    }

    public function insertCadavre($titre, $dateDebut, $dateFin, $nbContributions, $nbJaime, $id)
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

    public function addContribution($texteContribution, $idAdmin, $idCadavre)
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