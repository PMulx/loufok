<?php

declare(strict_types=1);

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\Joueur;
use App\Model\CadavreModel;
use App\Model\JoueurAdministrateur;

class JoueurController extends Controller
{
    public function index($id)
    {

        $cadavreModel = new CadavreModel();

        $currentCadavre = $cadavreModel->getCurrentCadavre();
        var_dump($currentCadavre);
        exit();

        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $cadavres = Joueur::getInstance()->getContributionCount();
                $id = $_SESSION['user_id'];

                $dateActuelle = date('Y-m-d');

                $this->display(
                    'joueur/listes.html.twig',
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

    public function insertaleatoire($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];
                $idCadavre = $_POST['cadavreEnCoursID'];
                $nbAleatoire = $_POST['nbAleatoire'];

                Joueur::getInstance()->ajouterAleatoire($nbAleatoire, $id, $idCadavre);

                HTTP::redirect("/joueur/cadavre/{$id}/{$idCadavre}");
            }
        } else {
            HTTP::redirect('/');
        }
    }

    public function cadavre($id, $idcadavre)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'administrateur') {
                HTTP::redirect("/administrateur/{$id}");
            } else {
                $id = $_SESSION['user_id'];
                $contributions = Joueur::getInstance()->getCadavreInfo($idcadavre);
                $contributionAleatoire = Joueur::getInstance()->getContributionAleatoireTexte($id, $idcadavre);
                $hiscontribution = Joueur::getInstance()->getContributionByIds($id, $idcadavre);

                $this->display(
                    'joueur/cadavre.html.twig',
                    [
                        'contributions' => $contributions,
                        'contributionAleatoire' => $contributionAleatoire,
                        'id' => $id,
                        'hiscontribution' => $hiscontribution,
                    ]
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

                Joueur::getInstance()->insererContribution($texteContribution, $ordreSoumission, $dateSoumission, $idcadavre, $id);

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

                $lastCadavre = JoueurAdministrateur::getInstance()->getLastFinishedCadavre($id);
                var_dump($lastCadavre);
                if ($lastCadavre) {
                    // Le dernier cadavre terminé a été trouvé, vous pouvez l'afficher ou effectuer d'autres actions
                    var_dump($lastCadavre);
                    $this->display('joueur/lastcadavre.html.twig');
                } else {
                    // Aucun cadavre terminé n'a été trouvé, vous pouvez gérer cette situation selon vos besoins
                    $this->display('joueur/lastcadavre.html.twig');
                }
            }
        } else {
            HTTP::redirect('/');
        }
    }
}
