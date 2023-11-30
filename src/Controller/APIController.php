<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\HTTP;
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

            // Organise les données en structure JSON
            $formattedData = [];

            foreach ($cadavres as $cadavre) {
                $cadavreId = $cadavre['id_cadavre'];

                // Vérifie si le cadavre existe déjà dans $formattedData
                if (!isset($formattedData[$cadavreId])) {
                    // S'il n'existe pas, ajoute une nouvelle entrée pour le cadavre
                    $formattedData[$cadavreId] = [
                        'id_cadavre' => $cadavre['id_cadavre'],
                        'titre_cadavre' => $cadavre['titre_cadavre'],
                        'date_debut_cadavre' => $cadavre['date_debut_cadavre'],
                        'date_fin_cadavre' => $cadavre['date_fin_cadavre'],
                        'nb_jaime' => $cadavre['nb_jaime'],
                        'contributions' => [],
                    ];
                }

                // Ajoute la contribution actuelle au tableau "contributions" du cadavre
                $formattedData[$cadavreId]['contributions'][] = [
                    'id_contribution' => $cadavre['id_contribution'],
                    'texte_contribution' => $cadavre['texte_contribution'],
                    'nom_plume' => $cadavre['nom_plume'],
                ];
            }

            // Envoie le résultat en tant que réponse JSON sans échapper les caractères Unicode
            header('Content-Type: application/json');
            echo json_encode(array_values($formattedData), JSON_UNESCAPED_UNICODE);
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
            $cadavreModel->addLike($id, $newLikes);

            // Retourne la réponse JSON appropriée
            $jsonResponse = json_encode(['likes' => $newLikes], JSON_UNESCAPED_UNICODE);

            // Envoie le résultat en tant que réponse JSON
            header('Content-Type: application/json');
            HTTP::redirect("/api/cadavre/{$id}");
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
            if ($currentLikes === 0) {
                $updatedLikes = "Erreur le cadavre n'a actuellement pas de j'aime";
            } else {
                // Incrémente le nombre de likes
                $newLikes = $currentLikes - 1;

                // Appelle la méthode addLike pour mettre à jour le nombre de likes
                $updatedLikes = $cadavreModel->removeLike($id, $newLikes);
            }

            // Convertit le résultat en JSON sans échapper les caractères Unicode
            $jsonResponse = json_encode(['likes' => $updatedLikes], JSON_UNESCAPED_UNICODE);

            // Envoie le résultat en tant que réponse JSON
            header('Content-Type: application/json');
            HTTP::redirect("/api/cadavre/{$id}");
            exit;
        } catch (PDOException $e) {
            // Gère les erreurs
            http_response_code(500);
            echo 'Internal Server Error';
            exit;
        }
    }
}
