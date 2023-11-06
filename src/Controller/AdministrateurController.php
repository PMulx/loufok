<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\JoueurAdministrateur;

class AdministrateurController extends Controller
{
    public function index($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'joueur') {
                HTTP::redirect("/joueur/{$id}");
            } else {
                $cadavres = JoueurAdministrateur::getInstance();
                $id = $_SESSION['user_id'];

                $dateActuelle = date('Y-m-d');

                $this->display(
                    'administrateur/admin.html.twig',
                    [
                      'dateActuelle' => $dateActuelle,
                      'cadavres' => $cadavres,
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
            $titre = $_POST['titre'];
            $dateDebut = $_POST['dateDebut'];
            $dateFin = $_POST['dateFin'];
            $nbContributions = $_POST['nbContributions'];
            $nbJaime = $_POST['nbJaime'];
            $texteContribution = $_POST['texteContribution'];

            $idCadavre = Administrateur::getInstance()->ajouterCadavre($titre, $dateDebut, $dateFin, $nbContributions, $nbJaime, $id);

            Administrateur::getInstance()->ajouterContribution($texteContribution, $id, $idCadavre);

            HTTP::redirect("/administrateur/{$id}");
        } else {
            var_dump('ERROR');
        }
    }
}
