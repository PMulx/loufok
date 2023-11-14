<?php

namespace App\Model;

class CadavreModel extends Model
{
    protected $cadavretableName = APP_TABLE_PREFIX.'cadavre';
    protected $contributiontableName = APP_TABLE_PREFIX.'contribution';
    protected $randomcontributiontableName = APP_TABLE_PREFIX.'contribution_aléatoire';
    protected $joueurtableName = APP_TABLE_PREFIX.'joueur';

    protected static $instance;

    protected function checkTextSize($text)
    {
        $textLength = strlen($text);

        return $textLength >= 50 && $textLength <= 280;
    }

    protected function isTitleUnique($title)
    {
        $sql = "SELECT COUNT(*) as title_count FROM {$this->cadavretableName} WHERE titre_cadavre = :title";
        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':title', $title);
        $sth->execute();
        $result = $sth->fetch();
        $titleCount = $result['title_count'];

        return $titleCount;
    }

    protected function isPeriodValid($dateStart, $dateEnd)
    {
        $sql = "SELECT COUNT(*) FROM {$this->cadavretableName} WHERE
                (date_debut_cadavre BETWEEN :dateStart AND :dateEnd) OR
                (date_fin_cadavre BETWEEN :dateStart AND :dateEnd)";
        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':dateStart', $dateStart);
        $sth->bindParam(':dateEnd', $dateEnd);
        $sth->execute();
        $count = $sth->fetchColumn();

        return $count;
    }

    protected function isNbMaxContributionsValid($nbMaxContributions)
    {
        return $nbMaxContributions > 1;
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getRandomContribution()
    {
        // Obtenez le résultat de getCurrentCadavreId
        $cadavreResult = $this->getCurrentCadavreId();

        if ($cadavreResult) {
            $id_cadavre = $cadavreResult;
            $id_joueur = $_SESSION['user_id'];

            $sql = "SELECT rc.id_cadavre, rc.id_joueur
                    FROM {$this->randomcontributiontableName} rc
                    WHERE rc.id_cadavre = :id_cadavre AND rc.id_joueur = :id_joueur";

            $sth = self::$dbh->prepare($sql);
            $sth->bindParam(':id_cadavre', $id_cadavre, \PDO::PARAM_INT);
            $sth->bindParam(':id_joueur', $id_joueur, \PDO::PARAM_INT);
            $sth->execute();
            $result = $sth->fetch();

            return $result;
        }

        return null; // Ou faites quelque chose d'autre en cas d'échec pour obtenir le cadavre
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

    public function getCurrentSubmissionOrder()
    {
        $currentCadavreId = $this->getCurrentCadavreId();

        if (!$currentCadavreId) {
            return 0; // Le cadavre actuel n'existe pas
        }

        $sql = "SELECT COUNT(ordre_soumission)
        FROM {$this->contributiontableName}
        WHERE id_cadavre = :id_cadavre";
        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':id_cadavre', $currentCadavreId);
        $sth->execute();
        $result = $sth->fetchColumn();

        return $result;
    }

    public function addJoueurContribution($cadavreId, $joueurId, $text)
    {
        if (!$this->checkTextSize($text)) {
            return "La longueur du texte n'est pas bonne.";
        }

        $currentCadavreId = $this->getCurrentCadavreId();
        $currentSubmissionOrder = $this->getCurrentSubmissionOrder();
        $nextSubmissionOrder = $currentSubmissionOrder + 1;

        if ($currentCadavreId !== $cadavreId) {
            return "Le cadavre sélectionné n'est pas valide pour cette contribution.";
        }

        $sql = "INSERT INTO {$this->contributiontableName} (texte_contribution, ordre_soumission, date_soumission, id_joueur, id_cadavre)
    VALUES (:text, 1, NOW(), :joueurId, :cadavreId)";
        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':text', $text);

        $sth->bindParam(':joueurId', $joueurId);
        $sth->bindParam(':cadavreId', $cadavreId);
        $sth->execute();
    }

    public function getCurrentCadavre($role, $id)
    {
        $id_joueur = $id;

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
        ORDER BY cad.id_cadavre, cont.ordre_soumission";
        }

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':role', $role);
        $alreadyPlayed = false;

        if ($role === 'joueur') {
            $sth->bindParam(':id_joueur', $id_joueur);
            $playedContributions = 0;

            $sth->execute();

            while ($row = $sth->fetch()) {
                if ($row['texte_contribution'] !== null) {
                    ++$playedContributions;
                }
            }

            if ($playedContributions === 2) {
                $alreadyPlayed = true;
            }
        }

        $sth->execute();

        return
            [
                'data' => $sth->fetchAll(),
                'played' => $alreadyPlayed,
            ];
    }

    public function createCadavre($title, $dateStart, $dateEnd, $adminId, $nbMaxContributions)
    {
        if ($this->isTitleUnique($title)) {
            return "Le titre n'est pas unique.";
        }

        if ($this->isPeriodValid($dateStart, $dateEnd)) {
            return 'La période se chevauche avec un autre cadavre.';
        }

        if (!$this->isNbMaxContributionsValid($nbMaxContributions)) {
            return 'Le nombre maximum de contributions doit être supérieur à 1.';
        }

        $sql = "INSERT INTO {$this->cadavretableName} (titre_cadavre, date_debut_cadavre, date_fin_cadavre, nb_contributions, nb_jaime, id_administrateur)
        VALUES (:title, :dateStart, :dateEnd, :nbMaxContributions, 0, :adminId)";
        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':title', $title);
        $sth->bindParam(':dateStart', $dateStart);
        $sth->bindParam(':dateEnd', $dateEnd);
        $sth->bindParam(':nbMaxContributions', $nbMaxContributions);
        $sth->bindParam(':adminId', $adminId);
        $sth->execute();

        return self::$dbh->lastInsertId();
    }

    public function addFirstContribution($cadavreId, $adminId, $text)
    {
        if (!$this->checkTextSize($text)) {
            return "La longueur du texte n'est pas bonne.";
        }

        $sql = "INSERT INTO {$this->contributiontableName} (texte_contribution, ordre_soumission, date_soumission, id_administrateur, id_cadavre)
        VALUES (:text, 1, NOW(), :adminId, :cadavreId)";
        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':text', $text);
        $sth->bindParam(':adminId', $adminId);
        $sth->bindParam(':cadavreId', $cadavreId);
        $sth->execute();
    }

    public function insertCadavreContribution($datas)
    {
        $title = $datas['title'];
        $dateStart = $datas['dateStart'];
        $dateEnd = $datas['dateEnd'];
        $text = $datas['text'];
        $adminId = $datas['adminId'];
        $nbMaxContributions = $datas['nbMaxContributions'];

        $cadavreId = $this->createCadavre($title, $dateStart, $dateEnd, $adminId, $nbMaxContributions);

        $this->addFirstContribution($cadavreId, $adminId, $text);
    }

    public function getAllTitles()
    {
        $sql = "SELECT titre_cadavre
        FROM {$this->cadavretableName}";

        $sth = self::$dbh->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }

    public function getAllPeriods()
    {
        $sql = "SELECT id_cadavre, CONCAT(date_debut_cadavre, ' | ', date_fin_cadavre) AS periode
                FROM {$this->cadavretableName}";

        $sth = self::$dbh->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }
}
