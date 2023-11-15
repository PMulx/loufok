<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\CadavreModel;

session_start();

class AdministrateurController extends Controller
{
    public function index($id)
    {
        $confirmMessages = isset($_SESSION['confirmMessages']) ? $_SESSION['confirmMessages'] : null;

        // Supprimez les messages de confirmation de la session pour qu'ils ne soient pas affichés à nouveau
        unset($_SESSION['confirmMessages']);

        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'joueur') {
                HTTP::redirect("/joueur/{$id}");
            } else {
                $dateActuelle = date('Y-m-d');

                $id = $_SESSION['user_id'];

                $cadavreModel = new CadavreModel();

                $periodesModel = $cadavreModel->getAllPeriods();
                $titles = $cadavreModel->getAllTitles();

                $periodes = [];
                foreach ($periodesModel as $periodeModel) {
                    $dateParts = explode(' | ', $periodeModel['periode']);
                    $periodes[] = [
                        'start' => $dateParts[0],
                        'end' => $dateParts[1],
                    ];
                }

                $this->display(
                    'administrateur/admin.html.twig',
                    [
                        'titles' => json_encode($titles),
                        'periodes' => $periodes,
                        'dateActuelle' => $dateActuelle,
                        'popup' => isset($_SESSION['popup']) ? $_SESSION['popup'] : null,
                        'id' => $id,
                        'confirmMessages' => $confirmMessages,
                    ]
                );
            }
        } else {
            HTTP::redirect('/');
        }
    }

    public function currentcadavre($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'joueur') {
                HTTP::redirect("/joueur/{$id}");
            } else {
                $id = $_SESSION['user_id'];

                $cadavreModel = new CadavreModel();

                $currentCadavres = $cadavreModel->getCurrentCadavre($role, $id);

                $this->display(
                    'administrateur/currentcadavre.html.twig',
                    [
                        'currentCadavres' => $currentCadavres,
                        'id' => $id,
                    ]
                );
            }
        } else {
            HTTP::redirect('/');
        }
    }

    public function add($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datas = [
                'title' => $_POST['titre'],
                'dateStart' => $_POST['dateDebut'],
                'dateEnd' => $_POST['dateFin'],
                'adminId' => $id,
                'text' => $_POST['texteContribution'],
                'nbMaxContributions' => $_POST['nbContributions'],
            ];

            $cadavreModel = new CadavreModel();
            $returnMessages = $cadavreModel->insertCadavreContribution($datas);

            $errorMessages = $returnMessages['errors'];
            $confirmMessages = $returnMessages['success'];

            if (!empty($errorMessages)) {

                $this->display(
                    'administrateur/error.html.twig',
                    [
                        'errorMessages' => $errorMessages,
                    ]
                );
            } else {
                $_SESSION['confirmMessages'] = $confirmMessages;

                HTTP::redirect("/administrateur/{$id}");
            }
        } else {
            var_dump('ERROR');
        }
    }
}
