<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\JoueurAdministrateurModel;
use App\Model\CadavreModel;
use App\Helper\HTTP;

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
        $dateActuelle = date("Y-m-d");

        $id = $_SESSION['user_id'];

        $cadavreModel = new CadavreModel();

        $currentCadavre = $cadavreModel->getCurrentCadavre();
        $periodesModel = $cadavreModel->getAllPeriods();
        $titles = $cadavreModel->getAllTitles();

        $periodes = [];
        foreach ($periodesModel as $periodeModel) {
          $dateParts = explode(" | ", $periodeModel['periode']);
          $periodes[] = [
            'start' => $dateParts[0],
            'end' => $dateParts[1],
          ];
        }


        $this->display(
          'administrateur/admin.html.twig',
          [
            'titles' => $titles,
            'currentCadavre' => $currentCadavre,
            'periodes' => $periodes,
            'dateActuelle' => $dateActuelle,
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

      $administrateurModel = new JoueurAdministrateurModel();

      HTTP::redirect("/administrateur/{$id}");
    } else {
      var_dump("ERROR");
    }
  }
}
