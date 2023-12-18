<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\CadavreModel;

class ApiController extends Controller
{
    public static function getAllCadavres()
    {
        try {
            $cadavres = CadavreModel::getInstance()->getAllFinishedCadavre();

            $result = ['cadavres' => $cadavres];

            header('Content-Type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (\PDOException $e) {
            http_response_code(500);
            echo 'Internal Server Error';
            exit;
        }
    }

    public static function getCadavre($id)
    {
        try {
            $cadavre = CadavreModel::getInstance()->getSingleCadavre($id);

            if ($cadavre === null) {
                http_response_code(404);
                echo 'Not Found';
                exit;
            }

            header('Content-Type: application/json');
            echo json_encode($cadavre, JSON_UNESCAPED_UNICODE);
            exit;
        } catch (\PDOException $e) {
            http_response_code(500);
            echo 'Internal Server Error';
            exit;
        }
    }

    public function addLike()
    {
        try {
            // Récupère le corps de la requête en tant que chaîne JSON
            $jsonPayload = file_get_contents('php://input');

            // Décodage de la chaîne JSON en tableau associatif
            $requestData = json_decode($jsonPayload, true);

            // Récupère l'id depuis les données décodées
            $id = $requestData['id'] ?? null;

            if ($id === null) {
                http_response_code(400);
                echo json_encode(['error' => 'Bad Request']);
                exit;
            }

            $cadavreModel = CadavreModel::getInstance();

            // Appelle la méthode addLike pour obtenir et mettre à jour le nombre de likes
            $newLikes = $cadavreModel->addLike($id);

            // Retourne la réponse JSON appropriée
            $jsonResponse = json_encode(['likes' => $newLikes], JSON_UNESCAPED_UNICODE);

            // Envoie le résultat en tant que réponse JSON
            header('Content-Length: '.strlen($jsonResponse));
            header('Content-Type: application/json');
            echo $jsonResponse;
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error: '.$e->getMessage()]);
            error_log($e->getTraceAsString()); // Ajout de la trace d'erreur aux logs
            exit;
        }
    }

    public static function removeLike()
    {
        try {
            $id = isset($_GET['id']) ? intval($_GET['id']) : null;

            if ($id === null) {
                http_response_code(400);
                echo json_encode(['error' => 'Bad Request']);
                exit;
            }

            $cadavreModel = CadavreModel::getInstance();
            $currentLikes = $cadavreModel->getLikesCadavre($id);

            if ($currentLikes === 0) {
                http_response_code(400);
                echo json_encode(['error' => "Erreur : le cadavre n'a actuellement pas de j'aime"]);
                exit;
            }

            $newLikes = $currentLikes - 1;
            $cadavreModel->removeLike($id, $newLikes);

            $jsonResponse = json_encode(['likes' => $newLikes], JSON_UNESCAPED_UNICODE);

            header('Content-Type: application/json');
            echo $jsonResponse;
            exit;
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
            exit;
        }
    }
}
