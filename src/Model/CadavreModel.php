<?php

namespace App\Model;

class CadavreModel extends Model
{
    protected $cadavretableName = APP_TABLE_PREFIX . 'cadavre';
    protected $contributiontableName = APP_TABLE_PREFIX . 'contribution';
    protected $randomcontributiontableName = APP_TABLE_PREFIX . 'contribution_aléatoire';
    protected $joueurtableName = APP_TABLE_PREFIX . 'joueur';

    protected static $instance;

    /**
     * Vérifie si la taille du texte est conforme aux limites définies.
     *
     * Cette méthode prend en paramètre un texte et vérifie sa longueur.
     * Le texte doit avoir une longueur d'au moins 50 caractères et ne pas dépasser 280 caractères,
     * conformément aux limites généralement associées à des champs de texte, tels que les messages.
     *
     * @param string $text le texte à vérifier
     *
     * @return bool true si la taille du texte est valide, sinon false
     */
    protected function checkTextSize($text)
    {
        // Obtient la longueur du texte.
        $textLength = strlen($text);

        // Vérifie si la longueur du texte est comprise entre 50 et 280 caractères inclus.
        return $textLength >= 50 && $textLength <= 280;
    }

    /**
     * Vérifie si le titre d'un cadavre est unique dans la base de données.
     *
     * Cette méthode prend en paramètre le titre d'un cadavre et effectue une requête SQL
     * pour compter le nombre de cadavres ayant ce titre. Si le nombre est égal à zéro, cela signifie
     * que le titre est unique et la méthode retourne true. Sinon, elle retourne false.
     *
     * @param string $title le titre du cadavre à vérifier
     *
     * @return bool true si le titre est unique, sinon false
     */
    protected function isTitleUnique($title)
    {
        // Définit la requête SQL pour compter le nombre de cadavres avec le titre spécifié.
        $sql = "SELECT COUNT(*) as title_count FROM {$this->cadavretableName} WHERE titre_cadavre = :title";

        // Prépare la requête SQL avec la connexion à la base de données.
        $sth = self::$dbh->prepare($sql);

        // Lie la valeur du paramètre de la requête SQL au paramètre fourni.
        $sth->bindParam(':title', $title);

        // Exécute la requête SQL.
        $sth->execute();

        // Récupère le résultat de la requête sous forme de tableau associatif.
        $result = $sth->fetch();

        // Récupère le nombre de cadavres avec le titre spécifié.
        $titleCount = $result['title_count'];

        // Retourne true si le titre est unique, sinon false.
        return $titleCount === 0;
    }

    /**
     * Vérifie si la période spécifiée pour un cadavre est valide et ne chevauche pas d'autres cadavres.
     *
     * Cette méthode prend en paramètre les dates de début et de fin d'un cadavre et vérifie si ces dates ne chevauchent pas
     * d'autres cadavres existants dans la base de données. Elle effectue une requête SQL pour compter le nombre de cadavres
     * dont la période (date de début ou date de fin) se situe entre les dates spécifiées. Si le nombre est égal à zéro,
     * cela signifie que la période est valide et la méthode retourne true. Sinon, elle retourne false.
     *
     * @param string $dateStart la date de début du cadavre
     * @param string $dateEnd   la date de fin du cadavre
     *
     * @return bool true si la période est valide, sinon false
     */
    protected function isPeriodValid($dateStart, $dateEnd)
    {
        // Définit la requête SQL pour compter le nombre de cadavres dont la période chevauche les dates spécifiées.
        $sql = "SELECT COUNT(*) FROM {$this->cadavretableName} WHERE
                (date_debut_cadavre BETWEEN :dateStart AND :dateEnd) OR
                (date_fin_cadavre BETWEEN :dateStart AND :dateEnd)";

        // Prépare la requête SQL avec la connexion à la base de données.
        $sth = self::$dbh->prepare($sql);

        // Lie les valeurs des paramètres de la requête SQL aux paramètres fournis.
        $sth->bindParam(':dateStart', $dateStart);
        $sth->bindParam(':dateEnd', $dateEnd);

        // Exécute la requête SQL.
        $sth->execute();

        // Récupère le résultat de la requête sous forme de colonne unique.
        $count = $sth->fetchColumn();

        // Retourne true si la période est valide (aucun chevauchement), sinon false.
        return $count === 0;
    }

    /**
     * Vérifie si le nombre maximal de contributions est valide.
     *
     * Cette méthode prend en paramètre le nombre maximal de contributions autorisé pour un cadavre
     * et vérifie s'il est supérieur à 1. Cela garantit qu'il y a une limite raisonnable au nombre de contributions possibles.
     *
     * @param int $nbMaxContributions le nombre maximal de contributions autorisé
     *
     * @return bool true si le nombre maximal de contributions est valide, sinon false
     */
    protected function isNbMaxContributionsValid($nbMaxContributions)
    {
        // Vérifie si le nombre maximal de contributions est supérieur à 1.
        return $nbMaxContributions > 1;
    }

    /**
     * Méthode statique pour obtenir une instance unique de la classe.
     *
     * Cette méthode implémente le modèle de conception Singleton, assurant qu'il n'y a qu'une seule instance
     * de la classe actuelle. Si aucune instance n'existe, elle en crée une et la retourne.
     *
     * @return joueurAdministrateurModel L'instance unique de la classe actuelle
     */
    public static function getInstance()
    {
        // Vérifie si une instance de la classe existe déjà.
        if (!isset(self::$instance)) {
            // Si aucune instance n'existe, crée une nouvelle instance de la classe.
            self::$instance = new self();
        }

        // Retourne l'instance existante ou nouvellement créée de la classe actuelle.
        return self::$instance;
    }

    /**
     * Obtient une contribution aléatoire pour le cadavre actuel et l'utilisateur en cours de session.
     *
     * Cette méthode utilise la méthode getCurrentCadavreId pour obtenir l'ID du cadavre actuel.
     * Ensuite, elle vérifie si l'utilisateur est en session (connecté) et exécute une requête SQL pour
     * récupérer une contribution aléatoire pour ce cadavre spécifique et cet utilisateur. Si une contribution est
     * trouvée, elle est renvoyée sous forme de tableau associatif. Sinon, la méthode retourne null.
     *
     * @return array|null le tableau associatif de la contribution aléatoire ou null en cas d'échec
     */
    public function getRandomContribution()
    {
        // Obtient le résultat de getCurrentCadavreId.
        $cadavreResult = $this->getCurrentCadavreId();

        // Vérifie si un cadavre est actuellement en cours.
        if ($cadavreResult) {
            // Obtient l'ID du cadavre et l'ID de l'utilisateur en session.
            $id_cadavre = $cadavreResult;
            $id_joueur = $_SESSION['user_id'];

            // Définit la requête SQL pour récupérer une contribution aléatoire pour le cadavre et l'utilisateur spécifiés.
            $sql = "SELECT rc.id_cadavre, rc.id_joueur
                    FROM {$this->randomcontributiontableName} rc
                    WHERE rc.id_cadavre = :id_cadavre AND rc.id_joueur = :id_joueur";

            // Prépare la requête SQL avec la connexion à la base de données.
            $sth = self::$dbh->prepare($sql);

            // Lie les valeurs des paramètres de la requête SQL aux paramètres fournis.
            $sth->bindParam(':id_cadavre', $id_cadavre, \PDO::PARAM_INT);
            $sth->bindParam(':id_joueur', $id_joueur, \PDO::PARAM_INT);

            // Exécute la requête SQL.
            $sth->execute();

            // Récupère le résultat de la requête sous forme de tableau associatif.
            $result = $sth->fetch();

            // Retourne le résultat de la requête ou null si aucune contribution n'est trouvée.
            return $result;
        }

        // Aucun cadavre en cours, retourne null.
        return null;
    }

    public function assignRandomContribution($id_cadavre, $id_joueur, $numContributionAleatoire)
    {
        // Insérer la contribution aléatoire dans la base de données
        $sql = "INSERT INTO {$this->randomcontributiontableName} (id_joueur, id_cadavre, num_contribution) VALUES (:id_joueur, :id_cadavre, :num_contribution)";
        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':id_joueur', $id_joueur);
        $sth->bindParam(':id_cadavre', $id_cadavre);
        $sth->bindParam(':num_contribution', $numContributionAleatoire);
        $sth->execute();
    }

    public function getCurrentCadavreId()
    {
        $sql = "SELECT c.id_cadavre
            FROM {$this->cadavretableName} c
            WHERE CURDATE() BETWEEN c.date_debut_cadavre AND c.date_fin_cadavre  AND c.nb_contributions > (SELECT COUNT(co.ordre_soumission) FROM {$this->contributiontableName} co WHERE co.id_cadavre = c.id_cadavre)";
        $sth = self::$dbh->prepare($sql);
        $sth->execute();
        $result = $sth->fetch();

        // Vérifiez si le résultat est un tableau non vide et que 'id_cadavre' est numérique
        if ($result && is_array($result) && is_numeric($result['id_cadavre'])) {
            // Retournez l'ID de cadavre en tant que chaîne
            return (string) $result['id_cadavre'];
        }

        // Si le résultat n'est pas conforme aux attentes, vous pouvez renvoyer null ou effectuer d'autres actions appropriées
        return null;
    }

    /**
     * Obtient l'ordre de soumission actuel pour le cadavre en cours.
     *
     * Cette méthode utilise la méthode getCurrentCadavreId pour obtenir l'ID du cadavre actuel.
     * Ensuite, elle effectue une requête SQL pour compter le nombre d'ordres de soumission déjà effectués
     * pour ce cadavre spécifique. Si le cadavre actuel n'existe pas, la méthode retourne 0.
     *
     * @return int L'ordre de soumission actuel pour le cadavre en cours, ou 0 si le cadavre n'existe pas
     */
    public function getCurrentSubmissionOrder()
    {
        // Obtient l'ID du cadavre actuel.
        $currentCadavreId = $this->getCurrentCadavreId();

        // Vérifie si un cadavre est actuellement en cours.
        if (!$currentCadavreId) {
            // Aucun cadavre en cours, retourne 0.
            return 0;
        }

        // Définit la requête SQL pour compter le nombre d'ordres de soumission pour le cadavre actuel.
        $sql = "SELECT COUNT(ordre_soumission)
        FROM {$this->contributiontableName}
        WHERE id_cadavre = :id_cadavre";

        // Prépare la requête SQL avec la connexion à la base de données.
        $sth = self::$dbh->prepare($sql);

        // Lie la valeur du paramètre de la requête SQL au paramètre fourni.
        $sth->bindParam(':id_cadavre', $currentCadavreId);

        // Exécute la requête SQL.
        $sth->execute();

        // Récupère le résultat de la requête sous forme de colonne unique.
        $result = $sth->fetchColumn();

        // Retourne le résultat de la requête.
        return $result;
    }

    /**
     * Ajoute la contribution d'un joueur à un cadavre.
     *
     * Cette méthode prend en paramètre l'ID du cadavre, l'ID du joueur, et le texte de la contribution.
     * Avant d'effectuer l'opération SQL, elle vérifie la taille du texte en utilisant la méthode checkTextSize.
     * Si la taille du texte n'est pas conforme, elle retourne un tableau d'erreurs. Sinon, elle tente d'ajouter la
     * contribution à la base de données en utilisant une requête SQL d'insertion. En cas d'erreur SQL, elle capture
     * l'exception PDOException, analyse le message d'erreur, et retourne un tableau d'erreurs spécifiques.
     *
     * @param int    $cadavreId L'ID du cadavre auquel ajouter la contribution
     * @param int    $joueurId  L'ID du joueur qui fait la contribution
     * @param string $text      le texte de la contribution
     *
     * @return array|null un tableau d'erreurs s'il y en a, sinon null
     */
    public function addJoueurContribution($cadavreId, $joueurId, $text)
    {
        // Initialise un tableau pour stocker les messages d'erreur.
        $errorMessages = [];

        // Vérifie la taille du texte avant d'effectuer l'opération SQL.
        if (!$this->checkTextSize($text)) {
            $errorMessages[] = "La longueur du texte n'est pas conforme.";

            // Retourne le tableau d'erreurs.
            return $errorMessages;
        }

        try {
            // Obtient l'ordre de soumission actuel pour le cadavre.
            $currentSubmissionOrder = $this->getCurrentSubmissionOrder();

            // Calcule le prochain ordre de soumission.
            $nextSubmissionOrder = $currentSubmissionOrder + 1;

            // Définit la requête SQL pour ajouter la contribution à la base de données.
            $sql = "INSERT INTO {$this->contributiontableName} (texte_contribution, ordre_soumission, date_soumission, id_joueur, id_cadavre)
            VALUES (:text, :nextSubmission, NOW(), :joueurId, :cadavreId)";

            // Prépare la requête SQL avec la connexion à la base de données.
            $sth = self::$dbh->prepare($sql);

            // Lie les valeurs des paramètres de la requête SQL aux paramètres fournis.
            $sth->bindParam(':text', $text);
            $sth->bindParam(':nextSubmission', $nextSubmissionOrder);
            $sth->bindParam(':joueurId', $joueurId);
            $sth->bindParam(':cadavreId', $cadavreId);

            // Exécute la requête SQL.
            $sth->execute();
        } catch (\PDOException $e) {
            // Récupère le message d'erreur SQL ici.
            $errorMessage = $e->getMessage();

            // Vérifie si le message d'erreur contient la chaîne spécifique liée à l'unicité de la contribution.
            if (strpos($errorMessage, "'uc_contributions'") !== false) {
                $errorMessages[] = "Il y a une erreur dans l'ajout de la contribution. Il semblerait que vous avez déjà joué sur ce Cadavre Exquis ou que le cadavre est terminé.";
            } else {
                // Traite les autres erreurs SQL comme nécessaire.
                $errorMessages[] = $errorMessage;
            }

            // Retourne le tableau d'erreurs.
            return $errorMessages;
        }

        // Retourne null si l'opération a réussi sans erreurs.
        return null;
    }

    /**
     * Obtient les informations du cadavre en cours pour un utilisateur donné.
     *
     * Cette méthode prend en paramètre le rôle de l'utilisateur ('administrateur' ou 'joueur') ainsi que son ID.
     * En fonction du rôle, elle exécute une requête SQL pour récupérer les contributions actuelles pour un administrateur
     * ou les contributions disponibles pour un joueur. Elle retourne un tableau contenant les données récupérées
     * ainsi qu'un indicateur indiquant si le joueur a déjà participé.
     *
     * @param string $role le rôle de l'utilisateur ('administrateur' ou 'joueur')
     * @param int    $id   L'ID de l'utilisateur
     *
     * @return array tableau contenant les données du cadavre et un indicateur si le joueur a déjà participé
     */
    public function getCurrentCadavre($role, $id)
    {
        // Initialise les variables.
        $id_joueur = $id;
        $alreadyPlayed = false;

        // Définit les requêtes SQL en fonction du rôle de l'utilisateur.
        if ($role === 'administrateur') {
            $sql = "SELECT
                    CASE
                        WHEN c.id_joueur IS NOT NULL THEN j.nom_plume
                        ELSE 'Administrateur'
                    END AS nom_plume,
                    c.texte_contribution,
                    c.ordre_soumission,
                    ca.titre_cadavre
                FROM {$this->contributiontableName} c
                LEFT JOIN {$this->cadavretableName} ca ON c.id_cadavre = ca.id_cadavre
                LEFT JOIN {$this->joueurtableName} j ON c.id_joueur = j.id_joueur
                WHERE ca.date_debut_cadavre <= CURDATE() AND ca.date_fin_cadavre >= CURDATE()
                AND :role = 'administrateur'";
        } elseif ($role === 'joueur') {
            $sql = "SELECT 
            CASE 
                WHEN cont.id_joueur = :id_joueur THEN cont.texte_contribution
                WHEN cr.num_contribution = cont.ordre_soumission THEN cont.texte_contribution
                ELSE NULL
            END AS texte_contribution,
            cont.ordre_soumission
        FROM {$this->cadavretableName} cad
        LEFT JOIN {$this->randomcontributiontableName} cr ON cad.id_cadavre = cr.id_cadavre AND cr.id_joueur = :id_joueur
        LEFT JOIN {$this->contributiontableName} cont ON cad.id_cadavre = cont.id_cadavre
        WHERE 
            CURDATE() BETWEEN cad.date_debut_cadavre AND cad.date_fin_cadavre
            AND cad.nb_contributions > (SELECT COUNT(cont.ordre_soumission) FROM {$this->contributiontableName} cont WHERE cont.id_cadavre = cad.id_cadavre)
        ORDER BY cad.id_cadavre, cont.ordre_soumission";
        }

        // Prépare la requête SQL avec la connexion à la base de données.
        $sth = self::$dbh->prepare($sql);

        // Lie les valeurs des paramètres de la requête SQL aux paramètres fournis.
        $sth->bindParam(':role', $role);

        if ($role === 'joueur') {
            $sth->bindParam(':id_joueur', $id_joueur);
            $playedContributions = 0;

            // Exécute la requête SQL et compte les contributions déjà jouées par le joueur.
            $sth->execute();

            while ($row = $sth->fetch()) {
                if ($row['texte_contribution'] !== null) {
                    ++$playedContributions;
                }
            }

            // Met à jour l'indicateur si le joueur a déjà joué deux contributions.
            if ($playedContributions === 2) {
                $alreadyPlayed = true;
            }
        }

        // Exécute la requête SQL.
        $sth->execute();

        // Retourne un tableau avec les données du cadavre et l'indicateur de participation.
        return [
            'data' => $sth->fetchAll(),
            'played' => $alreadyPlayed,
        ];
    }

    /**
     * Crée un nouveau cadavre avec les informations fournies.
     *
     * Cette méthode prend en paramètre le titre, la date de début, la date de fin, l'ID de l'administrateur,
     * et le nombre maximal de contributions autorisées. Avant d'effectuer l'opération SQL, elle vérifie
     * l'unicité du titre, la validité de la période, et le nombre maximal de contributions autorisées.
     * Si des erreurs sont détectées, elle retourne un tableau d'erreurs. Sinon, elle crée un nouveau cadavre
     * en utilisant une requête SQL d'insertion et retourne l'ID du cadavre créé.
     *
     * @param string $title              le titre du cadavre
     * @param string $dateStart          la date de début du cadavre
     * @param string $dateEnd            la date de fin du cadavre
     * @param int    $adminId            L'ID de l'administrateur créant le cadavre
     * @param int    $nbMaxContributions le nombre maximal de contributions autorisé
     *
     * @return array|int un tableau d'erreurs s'il y en a, sinon l'ID du cadavre créé
     */
    public function createCadavre($title, $dateStart, $dateEnd, $adminId, $nbMaxContributions)
    {
        // Initialise un tableau pour stocker les messages d'erreur.
        $errorMessages = [];

        // Vérifie l'unicité du titre.
        if ($this->isTitleUnique($title)) {
            $errorMessages[] = 'Le titre a déjà été utilisé.';
        }

        // Vérifie la validité de la période.
        if ($this->isPeriodValid($dateStart, $dateEnd)) {
            $errorMessages[] = 'La période se chevauche avec un autre cadavre.';
        }

        // Vérifie le nombre maximal de contributions autorisées.
        if (!$this->isNbMaxContributionsValid($nbMaxContributions)) {
            $errorMessages[] = 'Le nombre maximum de contributions doit être supérieur à 1.';
        }

        // Si des erreurs sont détectées, retourne le tableau d'erreurs.
        if (!empty($errorMessages)) {
            return $errorMessages;
        }

        // Prépare la requête SQL d'insertion avec la connexion à la base de données.
        $sql = "INSERT INTO {$this->cadavretableName} (titre_cadavre, date_debut_cadavre, date_fin_cadavre, nb_contributions, nb_jaime, id_administrateur)
        VALUES (:title, :dateStart, :dateEnd, :nbMaxContributions, 0, :adminId)";
        $sth = self::$dbh->prepare($sql);

        // Lie les valeurs des paramètres de la requête SQL aux paramètres fournis.
        $sth->bindParam(':title', $title);
        $sth->bindParam(':dateStart', $dateStart);
        $sth->bindParam(':dateEnd', $dateEnd);
        $sth->bindParam(':nbMaxContributions', $nbMaxContributions);
        $sth->bindParam(':adminId', $adminId);

        // Exécute la requête SQL d'insertion.
        $sth->execute();

        // Retourne l'ID du cadavre créé.
        return self::$dbh->lastInsertId();
    }

    /**
     * Ajoute la première contribution à un cadavre.
     *
     * Cette méthode prend en paramètre l'ID du cadavre, l'ID de l'administrateur qui ajoute la contribution,
     * et le texte de la première contribution. Avant d'effectuer l'opération SQL, elle vérifie la taille du texte.
     * Si la taille du texte ne correspond pas aux critères définis, elle lance une exception avec un message d'erreur.
     * Sinon, elle insère la contribution dans la base de données avec une requête SQL d'insertion et retourne
     * l'ID de la contribution créée.
     *
     * @param int    $cadavreId L'ID du cadavre auquel ajouter la première contribution
     * @param int    $adminId   L'ID de l'administrateur qui ajoute la contribution
     * @param string $text      le texte de la première contribution
     *
     * @return int L'ID de la contribution créée
     *
     * @throws \Exception si la taille du texte ne correspond pas aux critères définis
     */
    public function addFirstContribution($cadavreId, $adminId, $text)
    {
        // Vérifie la taille du texte avant d'effectuer l'opération SQL.
        if (!$this->checkTextSize($text)) {
            // Lance une exception avec un message d'erreur si la taille du texte est incorrecte.
            throw new \Exception("La longueur du texte n'est pas bonne.");
        }

        // Prépare la requête SQL d'insertion avec la connexion à la base de données.
        $sql = "INSERT INTO {$this->contributiontableName} (texte_contribution, ordre_soumission, date_soumission, id_administrateur, id_cadavre)
        VALUES (:text, 1, NOW(), :adminId, :cadavreId)";
        $sth = self::$dbh->prepare($sql);

        // Lie les valeurs des paramètres de la requête SQL aux paramètres fournis.
        $sth->bindParam(':text', $text);
        $sth->bindParam(':adminId', $adminId);
        $sth->bindParam(':cadavreId', $cadavreId);

        // Exécute la requête SQL d'insertion.
        $sth->execute();

        // Retourne l'ID de la contribution créée.
        return self::$dbh->lastInsertId();
    }

    /**
     * Supprime un cadavre de la base de données en cas d'erreur.
     *
     * Cette méthode prend en paramètre l'ID du cadavre à supprimer de la base de données.
     * Elle exécute une requête SQL de suppression pour retirer le cadavre de la table correspondante.
     * Cette méthode est généralement utilisée en cas d'erreur lors de la création d'un cadavre.
     *
     * @param int $cadavreId L'ID du cadavre à supprimer
     */
    public function deleteCadavreOnError($cadavreId)
    {
        // Prépare la requête SQL de suppression avec la connexion à la base de données.
        $sql = "DELETE FROM {$this->cadavretableName} WHERE id_cadavre = :cadavreId";
        $sth = self::$dbh->prepare($sql);

        // Lie la valeur du paramètre de la requête SQL au paramètre fourni.
        $sth->bindParam(':cadavreId', $cadavreId);

        // Exécute la requête SQL de suppression.
        $sth->execute();
    }

    public function insertCadavreContribution($datas)
    {
        $messages = [
            'errors' => [],     // Pour stocker les messages d'erreur
            'success' => null,  // Pour stocker le message de confirmation
        ];

        // Créer le cadavre
        $cadavreIdOrError = $this->createCadavre($datas['title'], $datas['dateStart'], $datas['dateEnd'], $datas['adminId'], $datas['nbMaxContributions']);

        if (is_array($cadavreIdOrError)) {
            // S'il y a des erreurs avec createCadavre, ajoutez-les au tableau d'erreurs
            $messages['errors'] = array_merge($messages['errors'], $cadavreIdOrError);
        } else {
            // Si createCadavre réussit, essayez d'ajouter la contribution
            try {
                $this->addFirstContribution($cadavreIdOrError, $datas['adminId'], $datas['text']);
                $messages['success'] = 'Le cadavre et la contribution ont été ajoutés avec succès.';
            } catch (\Exception $e) {
                // Attrapez l'exception de addFirstContribution et ajoutez le message d'erreur au tableau d'erreurs
                $messages['errors'][] = $e->getMessage();
                $this->deleteCadavreOnError($cadavreIdOrError);
            }
        }

        return $messages;
    }

    /**
     * Récupère tous les titres des cadavres existants.
     *
     * Cette méthode effectue une requête SQL SELECT pour obtenir tous les titres des cadavres présents dans la base de données.
     * Elle retourne un tableau contenant les résultats de la requête.
     *
     * @return array un tableau contenant tous les titres des cadavres
     */
    public function getAllTitles()
    {
        // Requête SQL pour récupérer tous les titres des cadavres.
        $sql = "SELECT titre_cadavre
        FROM {$this->cadavretableName}";

        // Prépare la requête SQL avec la connexion à la base de données.
        $sth = self::$dbh->prepare($sql);

        // Exécute la requête SQL.
        $sth->execute();

        // Retourne tous les titres des cadavres sous forme de tableau.
        return $sth->fetchAll();
    }

    /**
     * Récupère toutes les périodes (dates de début et de fin) des cadavres existants.
     *
     * Cette méthode effectue une requête SQL SELECT pour obtenir les identifiants des cadavres ainsi que leurs périodes
     * (concaténation des dates de début et de fin) présents dans la base de données. Elle retourne un tableau contenant
     * les résultats de la requête.
     *
     * @return array un tableau contenant les identifiants des cadavres et leurs périodes
     */
    public function getAllPeriods()
    {
        // Requête SQL pour récupérer les identifiants et les périodes des cadavres.
        $sql = "SELECT id_cadavre, CONCAT(date_debut_cadavre, ' | ', date_fin_cadavre) AS periode
                FROM {$this->cadavretableName}";

        // Prépare la requête SQL avec la connexion à la base de données.
        $sth = self::$dbh->prepare($sql);

        // Exécute la requête SQL.
        $sth->execute();

        // Retourne les identifiants et les périodes des cadavres sous forme de tableau.
        return $sth->fetchAll();
    }
}
