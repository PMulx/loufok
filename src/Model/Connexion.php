<?php

namespace App\Model;

class Connexion extends Model
{
    protected $tableJoueur = APP_TABLE_PREFIX . 'joueur';
    protected $tableAdministration = APP_TABLE_PREFIX . 'administrateur';

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
        $sql = "SELECT id_joueur AS id, ad_mail_joueur AS email, mot_de_passe_joueur AS mot_de_passe, 'joueur' AS type
                FROM joueur
                WHERE ad_mail_joueur = :email AND mot_de_passe_joueur = :password
                UNION
                SELECT id_administrateur AS id, ad_mail_administrateur AS email, mot_de_passe_administrateur AS mot_de_passe, 'administrateur' AS type
                FROM administrateur
                WHERE ad_mail_administrateur = :email AND mot_de_passe_administrateur = :password";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':email', $email);
        $sth->bindParam(':password', $password);
        $sth->execute();

        return $sth->fetch();
    }

    public function createUser($nomPlume, $email, $sexe, $ddn, $motDePasse)
    {
        $sql = "INSERT INTO joueur (nom_plume, ad_mail_joueur, sexe, ddn, mot_de_passe_joueur)
                VALUES (:nomPlume, :email, :sexe, :ddn, :motDePasse)";

        $sth = self::$dbh->prepare($sql);
        $sth->bindParam(':nomPlume', $nomPlume);
        $sth->bindParam(':email', $email);
        $sth->bindParam(':sexe', $sexe);
        $sth->bindParam(':ddn', $ddn);
        $sth->bindParam(':motDePasse', $motDePasse);
        $sth->execute();
    }

    public function getAllNomPlume()
    {
        $sql = "SELECT nom_plume FROM joueur";

        $sth = self::$dbh->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }
}
