<?php

namespace App\Model;

class CadavreModel extends Model
{
    protected $cadavretableName = APP_TABLE_PREFIX . 'cadavre';
    protected $contributiontableName = APP_TABLE_PREFIX . 'contribution';
    protected $joueurtableName = APP_TABLE_PREFIX . 'joueur';

    protected static $instance;

    protected function checkTextSize($text)
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getCurrentCadavre()
    {
        $role = $_SESSION['role'];
        $id_joueur = $_SESSION['user_id'];

        if ($role === 'administrateur') {
            $sql = "SELECT
                    CASE
                        WHEN c.id_joueur IS NOT NULL THEN j.nom_plume
                        ELSE 'Administrateur'
                    END AS nom_plume,
                    c.texte_contribution,
                    c.ordre_soumission
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
        FROM cadavre cad
        LEFT JOIN contribution_aléatoire cr ON cad.id_cadavre = cr.id_cadavre AND cr.id_joueur = :id_joueur
        LEFT JOIN contribution cont ON cad.id_cadavre = cont.id_cadavre
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

            while ($row = $sth->fetch()) {
                if ($row['texte_contribution'] !== null) {
                    $playedContributions++;
                }
            }

            if ($playedContributions = 2) {
                $alreadyPlayed = true;
            }
        }

        $sth->execute();

        return
            [
                "data" => $sth->fetchAll(),
                "played" => $alreadyPlayed,
            ];
    }

    public function insertCadavreContribution($datas)
    {
    }

    public function getAllTitles()
    {
        $sql = "SELECT titre_cadavre
        FROM cadavre";

        $sth = self::$dbh->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }

    public function getAllPeriods()
    {
        $sql = "SELECT id_cadavre, CONCAT(date_debut_cadavre, ' | ', date_fin_cadavre) AS periode
                FROM cadavre";

        $sth = self::$dbh->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }
}
