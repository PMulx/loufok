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

            $cadavreAdd = $cadavreModel->insertCadavreContribution($datas);
            if ($cadavreAdd) {
                $_SESSION['popup'] = "L'enregistrement de votre Cadavre a échoué.";
            } else {
                $_SESSION['popup'] = 'Votre cadavre a bien été enregistré.';
            }

            HTTP::redirect("/administrateur/{$id}");
        } else {
            var_dump('ERROR');
        }
    }
}
