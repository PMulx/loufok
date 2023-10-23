<?php

namespace App\Model;

class Connexion extends Model
{
    protected $tableName = APP_TABLE_PREFIX . 'etudiant';
    protected $tableFormation = APP_TABLE_PREFIX . 'formation';

    protected static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function authenticateUser($email, $password)
    {
        $sql = "SELECT id_etudiant AS id, email_etudiant AS email, mp_etudiant AS mot_de_passe, 'etudiant' AS type
                FROM etudiant
                WHERE email_etudiant = :email AND mp_etudiant = :password
                UNION
                SELECT id_formation AS id, email_resp_stage AS email, mp_resp_stage AS mot_de_passe, 'formation' AS type
                FROM formation
                WHERE email_resp_stage = :email AND mp_resp_stage = :password";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':email', $email);
        $sth->bindParam(':password', $password);
        $sth->execute();

        return $sth->fetch();
    }
}