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

      $datas = [
        'title' => $_POST['titre'],
        'dateStart' => $_POST['dateDebut'],
        'dateEnd' => $_POST['dateFin'],
        'adminId' => $id,
        'text' => $_POST['texteContribution'],
        'nbMaxContributions' => $_POST['nbContributions']
      ];

      $cadavreModel = new CadavreModel();
      $InsertCadavreContribution = $cadavreModel->insertCadavreContribution($datas);

      var_dump($InsertCadavreContribution);

      HTTP::redirect("/administrateur/{$id}");
    } else {
      var_dump("ERROR");
    }
  }
}
