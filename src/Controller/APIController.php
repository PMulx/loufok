<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\CadavreModel;

session_start();

class ApiController extends Controller
{
    /**
     * Affiche la page principale du joueur.
     */
    public static function getAllCadavres()
    {
        try {
            $cadavres = CadavreModel::getInstance()->getAllFinishedCadavre();

            // Met le résultat dans un tableau nommé $result
            $result = ['cadavres' => $cadavres];

            // Envoie le résultat en tant que réponse JSON sans échapper les caractères Unicode
            header('Content-Type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
            // Utilise la nouvelle méthode pour obtenir un seul cadavre
            $cadavre = CadavreModel::getInstance()->getSingleCadavre($id);

            // Vérifie si le cadavre existe
            if ($cadavre === null) {
                http_response_code(404);
                echo 'Not Found';
                exit;
            }

            // Envoie le résultat en tant que réponse JSON sans échapper les caractères Unicode
            header('Content-Type: application/json');
            echo json_encode($cadavre, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (PDOException $e) {
            http_response_code(500);
            echo 'Internal Server Error';
            exit;
        }
    }

    public static function addLike()
    {
        try {
            // Récupère le corps de la requête en tant que chaîne JSON
            $jsonPayload = file_get_contents('php://input');

            // Décodage de la chaîne JSON en tableau associatif
            $requestData = json_decode($jsonPayload, true);

            // Récupère l'id depuis les données décodées
            $id = isset($requestData['id_cadavre']) ? intval($requestData['id_cadavre']) : null;

            // Vérifie si l'id est présent dans les données
            if ($id === null) {
                http_response_code(400);
                echo 'Bad Request';
                exit;
            }

            // Récupère l'instance de CadavreModel
            $cadavreModel = CadavreModel::getInstance();

            // Appelle la méthode addLike pour obtenir et mettre à jour le nombre de likes
            $newLikes = $cadavreModel->addLike($id);

            // Retourne la réponse JSON appropriée
            $jsonResponse = json_encode(['likes' => $newLikes], JSON_UNESCAPED_UNICODE);

            // Envoie l'en-tête "Origin"
            header('Access-Control-Allow-Origin: *'); // Remplacez * par l'origine autorisée si possible

            // Envoie le résultat en tant que réponse JSON
            header('Content-Type: application/json');
            echo $jsonResponse;
            exit;
        } catch (PDOException $e) {
            // Gère les erreurs PDO
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            exit;
        } catch (Exception $e) {
            // Gère d'autres types d'erreurs
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            exit;
        }
    }

    public static function removeLike()
    {
        try {
            // Récupère l'id depuis les paramètres de l'URL
            $id = isset($_GET['id']) ? intval($_GET['id']) : null;

            // Vérifie si l'id est présent dans les paramètres
            if ($id === null) {
                http_response_code(400);
                echo 'Bad Request';
                exit;
            }

            // Récupère l'instance de CadavreModel
            $cadavreModel = CadavreModel::getInstance();

            // Récupère le nombre actuel de likes
            $currentLikes = $cadavreModel->getLikesCadavre($id);

            if ($currentLikes === 0) {
                // Si le nombre actuel de likes est déjà 0, retourne une erreur
                http_response_code(400);
                echo "Erreur : le cadavre n'a actuellement pas de j'aime";
                exit;
            }

            // Décrémente le nombre de likes
            $newLikes = $currentLikes - 1;

            // Appelle la méthode removeLike pour mettre à jour le nombre de likes
            $cadavreModel->removeLike($id, $newLikes);

            // Retourne la réponse JSON appropriée
            $jsonResponse = json_encode(['likes' => $newLikes], JSON_UNESCAPED_UNICODE);

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
