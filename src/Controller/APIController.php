<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\CadavreModel;

session_start();

class APIController extends Controller
{
    /**
     * Affiche la page principale du joueur.
     */
    public static function getAllCadavres()
    {
        try {
            $cadavres = CadavreModel::getInstance()->getAllFinishedCadavre();

            // Convertit le tableau en JSON sans échapper les caractères Unicode
            $jsonResponse = json_encode($cadavres, JSON_UNESCAPED_UNICODE);

            // Envoie le résultat en tant que réponse JSON
            header('Content-Type: application/json');
            echo $jsonResponse;
            exit;
        } catch (PDOException $e) {
            http_response_code(500);
            echo 'Internal Server Error';
            exit;
        }
    }

    public static function getCadavre($id)
    {
        try {
            $cadavres = CadavreModel::getInstance()->getFinishedCadavre($id);

            // Convertit le tableau en JSON sans échapper les caractères Unicode
            $jsonResponse = json_encode($cadavres, JSON_UNESCAPED_UNICODE);

            // Envoie le résultat en tant que réponse JSON
            header('Content-Type: application/json');
            echo $jsonResponse;
            exit;
        } catch (PDOException $e) {
            http_response_code(500);
            echo 'Internal Server Error';
            exit;
        }
    }

    public static function addLike($id)
    {
        try {
            // Récupère l'instance de CadavreModel
            $cadavreModel = CadavreModel::getInstance();

            // Récupère le nombre actuel de likes
            $currentLikes = $cadavreModel->getLikesCadavre($id);

            // Incrémente le nombre de likes
            $newLikes = $currentLikes + 1;

            // Appelle la méthode addLike pour mettre à jour le nombre de likes
            $updatedLikes = $cadavreModel->addLike($id, $newLikes);

            // Convertit le résultat en JSON sans échapper les caractères Unicode
            $jsonResponse = json_encode(['likes' => $updatedLikes], JSON_UNESCAPED_UNICODE);

            // Envoie le résultat en tant que réponse JSON
            header('Content-Type: application/json');
            echo $jsonResponse;
            exit;
        } catch (PDOException $e) {
            // Gère les erreurs
            http_response_code(500);
            echo 'Internal Server Error';
            exit;
        }
    }

    public static function removeLike($id)
    {
        try {
            // Récupère l'instance de CadavreModel
            $cadavreModel = CadavreModel::getInstance();

            // Récupère le nombre actuel de likes
            $currentLikes = $cadavreModel->getLikesCadavre($id);

            // Incrémente le nombre de likes
            $newLikes = $currentLikes - 1;

            // Appelle la méthode addLike pour mettre à jour le nombre de likes
            $updatedLikes = $cadavreModel->removeLike($id, $newLikes);

            // Convertit le résultat en JSON sans échapper les caractères Unicode
            $jsonResponse = json_encode(['likes' => $updatedLikes], JSON_UNESCAPED_UNICODE);

            // Envoie le résultat en tant que réponse JSON
            header('Content-Type: application/json');
            echo $jsonResponse;
            exit;
        } catch (PDOException $e) {
            // Gère les erreurs
            http_response_code(500);
            echo 'Internal Server Error';
            exit;
        }
    }
}
