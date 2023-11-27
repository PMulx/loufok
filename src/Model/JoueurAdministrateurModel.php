<?php

namespace App\Model;

class JoueurAdministrateurModel extends Model
{
    protected $tableAdminstrateur = APP_TABLE_PREFIX . 'administrateur';
    protected $tableJoueur = APP_TABLE_PREFIX . 'joueur';
    protected $tableContribution = APP_TABLE_PREFIX . 'contribution';
    protected $tableCadavre = APP_TABLE_PREFIX . 'cadavre';
    protected static $instance;

    /**
     * Méthode statique pour obtenir une instance unique de la classe.
     *
     * Cette méthode implémente le modèle de conception Singleton, assurant qu'il n'y a qu'une seule instance
     * de la classe JoueurAdministrateurModel. Si aucune instance n'existe, elle en crée une et la retourne.
     *
     * @return joueurAdministrateurModel L'instance unique de la classe JoueurAdministrateurModel
     */
    public static function getInstance()
    {
        // Vérifie si une instance de la classe existe déjà.
        if (!isset(self::$instance)) {
            // Si aucune instance n'existe, crée une nouvelle instance de la classe.
            self::$instance = new self();
        }

        // Retourne l'instance existante ou nouvellement créée de la classe JoueurAdministrateurModel.
        return self::$instance;
    }

    /**
     * Vérifie les informations de connexion d'un utilisateur (joueur ou administrateur).
     *
     * Cette méthode effectue une requête SQL pour récupérer les informations de l'utilisateur (joueur ou administrateur)
     * en se basant sur l'adresse e-mail et le mot de passe fournis. Utilise UNION pour combiner les résultats des tables
     * joueur et administrateur. Les informations incluses sont l'ID, l'e-mail, le mot de passe haché et le type d'utilisateur
     * (joueur ou administrateur).
     *
     * @param string $email    L'adresse e-mail de l'utilisateur
     * @param string $password le mot de passe de l'utilisateur
     *
     * @return mixed|null les informations de l'utilisateur ou null si non trouvé
     */
    public function checkLogin($email, $password)
    {
        // Définit la requête SQL pour récupérer les informations de connexion de l'utilisateur.
        $sql = "SELECT id_joueur AS id, nom_plume as nom, ad_mail_joueur AS email, mot_de_passe_joueur AS mot_de_passe, 'joueur' AS type
                FROM {$this->tableJoueur}
                WHERE ad_mail_joueur = :email AND mot_de_passe_joueur = :password
                UNION
                SELECT id_administrateur AS id, 'Admin' as nom, ad_mail_administrateur AS email, mot_de_passe_administrateur AS mot_de_passe, 'administrateur' AS type
                FROM {$this->tableAdminstrateur}
                WHERE ad_mail_administrateur = :email AND mot_de_passe_administrateur = :password";

        // Prépare la requête SQL avec la connexion à la base de données.
        $sth = self::$dbh->prepare($sql);

        // Lie les valeurs des paramètres de la requête SQL aux paramètres fournis.
        $sth->bindParam(':email', $email);
        $sth->bindParam(':password', $password);

        // Exécute la requête SQL.
        $sth->execute();

        // Retourne la première ligne de résultats sous forme de tableau associatif.
        return $sth->fetch();
    }

    /**
     * Déconnecte l'utilisateur en détruisant la session.
     *
     * Cette méthode effectue les opérations nécessaires pour déconnecter un utilisateur.
     * Elle commence par libérer toutes les variables de session avec la fonction session_unset(),
     * puis détruit la session avec session_destroy(). Enfin, elle ferme l'écriture de la session avec session_write_close().
     * Cette séquence garantit une déconnexion complète de l'utilisateur en effaçant les données de session et en rendant
     * la session inactive.
     */
    public function logout()
    {
        // Libère toutes les variables de session.
        session_unset();

        // Détruit la session.
        session_destroy();

        // Ferme l'écriture de la session.
        session_write_close();
    }

    /**
     * Obtient l'ID du dernier cadavre terminé par un joueur.
     *
     * Cette méthode effectue une requête SQL complexe pour récupérer l'ID du dernier cadavre terminé par un joueur spécifié.
     * Elle sélectionne l'ID du cadavre en vérifiant la date de fin du cadavre et en s'assurant que le nombre de contributions
     * atteint le nombre prévu. La sous-requête dans la clause WHERE compte le nombre de soumissions pour chaque cadavre.
     * La méthode utilise également une jointure entre les tables de cadavre et de contribution.
     * Si un cadavre est trouvé, l'ID est retourné. Sinon, la méthode retourne null, indiquant qu'aucun cadavre n'a été trouvé
     * pour cet utilisateur.
     *
     * @param int $id L'ID du joueur
     *
     * @return int|null L'ID du cadavre ou null s'il n'y a pas de cadavre terminé
     */
    public function getLastFinishedCadavre($id)
    {
        // Définit la requête SQL pour obtenir l'ID du dernier cadavre terminé par le joueur.
        $sql = "SELECT c.id_cadavre
                FROM {$this->tableCadavre} c
                JOIN {$this->tableContribution} co ON c.id_cadavre = co.id_cadavre
                WHERE (c.date_fin_cadavre < CURDATE() OR c.nb_contributions <= (SELECT COUNT(ordre_soumission) FROM {$this->tableContribution} WHERE id_cadavre = c.id_cadavre))
                AND co.id_joueur = :id_joueur
                ORDER BY c.date_fin_cadavre DESC
                LIMIT 1";

        // Prépare la requête SQL avec la connexion à la base de données.
        $sth = self::$dbh->prepare($sql);

        // Lie la valeur du paramètre de la requête SQL au paramètre fourni.
        $sth->bindParam(':id_joueur', $id);

        // Exécute la requête SQL.
        $sth->execute();

        // Récupère la première colonne du premier enregistrement retourné.
        $id_cadavre = $sth->fetchColumn();

        // Vérifie si un cadavre a été trouvé.
        if ($id_cadavre) {
            // Retourne l'ID du cadavre trouvé.
            return $id_cadavre;
        } else {
            // Aucun cadavre trouvé pour cet utilisateur, retourne null.
            return null;
        }
    }

    /**
     * Récupère les informations complètes d'un cadavre spécifique, y compris ses contributions et les noms d'auteurs.
     *
     * @param int $id L'identifiant du joueur pour lequel obtenir les informations du cadavre
     *
     * @return array|null un tableau contenant les informations complètes du cadavre ou null si l'id_cadavre est invalide ou inexistant
     */
    public function getCompleteCadavreInfo($id)
    {
        // Étape 1: Utiliser la méthode getLastFinishedCadavre pour obtenir l'id_cadavre
        $id_cadavre = $this->getLastFinishedCadavre($id);

        // Étape 2: Si vous obtenez un id_cadavre valide, récupérez les informations souhaitées
        if ($id_cadavre) {
            // Requête SQL pour récupérer les informations complètes du cadavre et de ses contributions
            $sql = "SELECT c.*, co.*, IFNULL(j.nom_plume, 'Administrateur') AS nom_plume
                FROM {$this->tableCadavre} c
                JOIN {$this->tableContribution} co ON c.id_cadavre = co.id_cadavre
                LEFT JOIN {$this->tableJoueur} j ON co.id_joueur = j.id_joueur
                WHERE c.id_cadavre = :id_cadavre";

            // Préparer et exécuter la requête SQL
            $sth = self::$dbh->prepare($sql);
            $sth->bindParam(':id_cadavre', $id_cadavre);
            $sth->execute();

            // Étape 3: Vous obtiendrez un ensemble de résultats avec toutes les informations souhaitées
            return $sth->fetchAll();
        } else {
            // Étape 4: Traitez le cas où l'id_cadavre est nul ou inexistant
            return null;
        }
    }
}
