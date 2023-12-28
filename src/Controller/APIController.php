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
            // indique au client qu'il est habilité à faire des demandes
            header('Access-Control-Allow-Origin: *');
            // indique au client les méthodes reconnues
            header('Access-Control-Allow-Headers: accept,content-type');
            header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
            // limiter le nombre de requêtes préliminaires en demandant
            // au navigateur à mettre en cache la requête pendant 1000 secondes
            header('Access-Control-Max-Age: 1000');
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
            // indique au client qu'il est habilité à faire des demandes
            header('Access-Control-Allow-Origin: *');
            // indique au client les méthodes reconnues
            header('Access-Control-Allow-Headers: accept,content-type');
            header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
            // limiter le nombre de requêtes préliminaires en demandant
            // au navigateur à mettre en cache la requête pendant 1000 secondes
            header('Access-Control-Max-Age: 1000');
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

            $cadavre = $cadavreModel->find($id);

            // Vérifie si le cadavre a été trouvé
            if (!$cadavre) {
                http_response_code(404);
                echo json_encode(['error' => 'Cadavre not found']);
                exit;
            }

            // Met à jour le nombre de likes
            $newLikes = $cadavre['nb_jaime'] + 1;

            $cadavreModel->update($id, [
                'nb_jaime' => $newLikes,
            ]);

            // Retourne la réponse JSON appropriée
            $jsonResponse = json_encode(['likes' => $newLikes], JSON_UNESCAPED_UNICODE);

            // indique au client qu'il est habilité à faire des demandes
            header('Access-Control-Allow-Origin: *');
            // indique au client les méthodes reconnues
            header('Access-Control-Allow-Headers: accept,content-type');
            header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
            // limiter le nombre de requêtes préliminaires en demandant
            // au navigateur à mettre en cache la requête pendant 1000 secondes
            header('Access-Control-Max-Age: 1000');
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

            $cadavre = $cadavreModel->find($id);

            // Vérifie si le cadavre a été trouvé
            if (!$cadavre) {
                http_response_code(404);
                echo json_encode(['error' => 'Cadavre not found']);
                exit;
            }

            // Met à jour le nombre de likes
            $newLikes = $cadavre['nb_jaime'] - 1;

            $cadavreModel->update($id, [
                'nb_jaime' => $newLikes,
            ]);

            // Retourne la réponse JSON appropriée
            $jsonResponse = json_encode(['likes' => $newLikes], JSON_UNESCAPED_UNICODE);

            // indique au client qu'il est habilité à faire des demandes
            header('Access-Control-Allow-Origin: *');
            // indique au client les méthodes reconnues
            header('Access-Control-Allow-Headers: accept,content-type');
            header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
            // limiter le nombre de requêtes préliminaires en demandant
            // au navigateur à mettre en cache la requête pendant 1000 secondes
            header('Access-Control-Max-Age: 1000');
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
}
