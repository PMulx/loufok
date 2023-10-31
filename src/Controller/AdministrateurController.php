<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\AdministrateurModel;
use App\Model\CadavreModel;
use App\Helper\HTTP;

class AdministrateurController extends Controller
{
  public function index($id)
  {
    session_start();

    if (isset($_SESSION['role'])) {
      $role = $_SESSION['role'];

      if ($role === 'joueur') {
        HTTP::redirect("/joueur/{$id}");
      } else {
        $dateActuelle = date("Y-m-d");

        $id = $_SESSION['user_id'];

        $cadavreModel = new CadavreModel();
        $periodesModel = $cadavreModel->getPeriodes();

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

      $administrateurModel = new AdministrateurModel();
      $idCadavre = $administrateurModel->ajouterCadavre($titre, $dateDebut, $dateFin, $nbContributions, $nbJaime);

      $administrateurModel->ajouterContribution($texteContribution, $idCadavre);

      HTTP::redirect("/administrateur/{$id}");
    } else {
      var_dump("ERROR");
    }
  }
}
