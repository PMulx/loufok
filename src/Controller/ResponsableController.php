<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\Responsable;
use Dompdf\Dompdf;

class ResponsableController extends Controller
{
  public function index($id)
  {
    if (isset($_SESSION['role'])) {
      $role = $_SESSION['role'];

      if ($role === 'etudiant') {
        HTTP::redirect("/etudiant/{$id}");
      } else {
        $id = $_SESSION['user_id'];
        $responsable = Responsable::getInstance()->getFormationInfo($id);

        $etudiants = Responsable::getInstance()->getEtudiantsInfos($id);

        $reglage = Responsable::getInstance()->formationReglages($id);

        $this->display(
          'responsables/inscriptions.html.twig',
          [
            'responsable' => $responsable,
            'etudiants' => $etudiants,
            'reglage' => $reglage,
          ]
        );
      }
    } else {
      HTTP::redirect('/');
    }
  }

  public function profil($id)
  {
    $id = $_SESSION['user_id'];

    $responsable = Responsable::getInstance()->getFormationInfo($id);

    $this->display(
      'responsables/profil.html.twig',
      [
        'responsable' => $responsable,
      ]
    );
  }

  public function generatePDFAction()
  {
    $id = $_SESSION['user_id'];
    $etudiants = Responsable::getInstance()->getEtudiantsInfos($id);
    $html = '<html>
    <head>
        <title>Fichier étudiants</title>
    </head>
    <body style="font-family: sans-serif;">
        <div class="container">
            <h1>Liste des étudiants</h1>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #313f4e; color: #fff;">
                        <th style="padding: 10px; min-width: 75px; text-align: left;">Nom Prénom</th>
                        <th style="padding: 10px; min-width: 75px; text-align: left;">Adresse Mail</th>
                        <th style="padding: 10px; min-width: 75px; text-align: left;">Nombre d\'entretiens</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($etudiants as $index => $etudiant) {
      $nom = $etudiant['nom_etudiant'];
      $prenom = $etudiant['prenom_etudiant'];
      $email = $etudiant['email_etudiant'];
      $nombreEntretiens = $etudiant['nombre_entretiens'];
      $entretienText = ($nombreEntretiens > 1) ? 'entretiens' : 'entretien';

      $backgroundColor = ($index % 2 == 0) ? '#eef0f4' : '#1b72f3';
      $fontColor = ($backgroundColor == '#1b72f3') ? '#fff' : '#333';

      $html .= "
                <tr style=\"background-color: $backgroundColor; color: $fontColor; text-align: left;\">
                    <td style=\"padding: 10px; min-width: 75px;\">$nom $prenom</td>
                    <td style=\"padding: 10px; min-width: 75px;\">$email</td>
                    <td style=\"padding: 10px; min-width: 75px;\">$nombreEntretiens $entretienText</td>
                </tr>";
    }

    $html .= '</tbody>
            </table>
        </div>
    </body>
</html>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();
    $dompdf->stream('etudiants.pdf', ['Attachment' => 0]);
  }

  public function edit($id)
  {
    $id = $_SESSION['user_id'];
    $reglage = Responsable::getInstance()->formationReglages($id);

    $this->display(
      'responsables/inscriptions.html.twig',
      [
        'reglage' => $reglage,
      ]
    );
  }

  public function update($id)
  {
    $id = $_SESSION['user_id'];
    $nouvelle_date_deb_insc = trim($_POST['date_deb_insc']);
    $nouvelle_date_fin_insc = trim($_POST['date_fin_insc']);
    $nouveau_nb_max_entretiens = trim($_POST['nb_max_entretiens']);

    Responsable::getInstance()->updateFormationReglages($id, $nouvelle_date_deb_insc, $nouvelle_date_fin_insc, $nouveau_nb_max_entretiens);

    HTTP::redirect("/responsable/{$id}");
  }
}
