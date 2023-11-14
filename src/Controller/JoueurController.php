<?php

declare(strict_types=1);

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\CadavreModel;
use App\Model\JoueurAdministrateurModel;

class JoueurController extends Controller
{
    public function index($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];

                $cadavreModel = new CadavreModel();
                $randomContribution = $cadavreModel::getInstance()->getRandomContribution();

                $maxOrdre = $cadavreModel::getInstance()->getCurrentSubmissionOrder();

                $currentCadavreContributions = $cadavreModel->getCurrentCadavre($role, $id);

                $dateActuelle = date('Y-m-d');

                $this->display(
                    'joueur/listes.html.twig',
                    [
                        'dateActuelle' => $dateActuelle,
                        'id' => $id,
                        'currentCadavreContributions' => $currentCadavreContributions,
                    ]
                );
            }
        } else {
            HTTP::redirect('/');
        }
    }

    // public function insertAleatoire($id)
    // {
    //     if (isset($_SESSION['role'])) {
    //         $role = $_SESSION['role'];
    //         $cadavre = Cadavre::getInstance()->getCurrentCadavreId();

    //         if ($role === 'administrateur') {
    //             HTTP::redirect("/administrateur/{$id}");
    //         } else {
    //             $existingContributionAleatoire = ContributionAleatoire::getInstance()->findby([
    //                 'id_joueur' => $_SESSION['user_id'],
    //                 'id_cadavre' => $cadavre,
    //             ]);

    //             if (empty($existingContributionAleatoire)) {
    //                 $maxOrdre = Contribution::getInstance()->getMaxOrdreContributionForCadavre($cadavre);

    //                 if ($maxOrdre >= 1) {
    //                     $numContributionAleatoire = mt_rand(1, $maxOrdre);

    //                     ContributionAleatoire::getInstance()->create([
    //                         'id_joueur' => $_SESSION['user_id'],
    //                         'id_cadavre' => $cadavre,
    //                         'num_contribution' => $numContributionAleatoire,
    //                     ]);
    //                 } else {
    //                     $errors = 'Le nombre maximum de contributions pour ce cadavre est invalide.';
    //                 }
    //             }
    //             HTTP::redirect("/joueur/cadavre/{$id}/{$cadavre}");
    //         }
    //     } else {
    //         HTTP::redirect('/');
    //     }
    // }

    public function cadavre($id, $idcadavre)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];

                $this->display(
                    'joueur/cadavre.html.twig',
                    []
                );
            }
        } else {
            HTTP::redirect('/');
        }
    }

    public function insertcontribution($id, $idcadavre)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $texteContribution = $_POST['texteContribution'];
                $ordreSoumission = $_POST['ordreSoumission'];
                $dateSoumission = date('Y-m-d');

                HTTP::redirect("/joueur/{$id}");
            }
        } else {
            HTTP::redirect('/');
        }
    }

    public function lastcadavre($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];

                $lastCadavre = JoueurAdministrateurModel::getInstance()->getCompleteCadavreInfo($id);
                if ($lastCadavre) {
                    $this->display(
                        'joueur/lastcadavre.html.twig',
                        [
                            'lastcadavre' => $lastCadavre,
                            'id' => $id,
                        ]
                    );
                } else {
                    HTTP::redirect("/joueur/{$id}");
                }
            }
        } else {
            HTTP::redirect('/');
        }
    }
}
