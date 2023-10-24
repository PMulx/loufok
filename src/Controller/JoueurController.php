<?php

declare(strict_types=1);

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\Etudiant;

class EtudiantController extends Controller
{
    public function index($id)
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            if ($role === 'responsable') {
                HTTP::redirect("/responsable/{$id}");
            } else {
                $formation = Etudiant::getInstance()->getDateParameters($id);
                $etudiant = Etudiant::getInstance()->etudiantData($id);
                $sesentretiens = Etudiant::getInstance()->sesEntretiens($id);
                $potentielEntretiens = Etudiant::getInstance()->potentielEntretiens($id);

                $entreprisesSesEntretiens = [];
                $fichiersOffreSesEntretiens = [];


                foreach ($sesentretiens as $sesentretien) {
                    $entrepriseData = [
                        'id_entreprise' => $sesentretien['id_entreprise'],
                        'nom_entreprise' => $sesentretien['nom_entreprise'],
                        'dpt_entreprise' => $sesentretien['dpt_entreprise'],
                    ];

                    $entreprisesSesEntretiens[] = $entrepriseData;

                    if (isset($sesentretien['fichiers_offre']) && is_array($sesentretien['fichiers_offre'])) {
                        $fichiersOffreSesEntretiens[] = $sesentretien['fichiers_offre'];
                    } else {
                        $fichiersOffreSesEntretiens[] = [];
                    }
                }

                $entreprisesPotentielEntretiens = [];
                $fichiersOffrePotentielEntretiens = [];

                foreach ($potentielEntretiens as $potentielEntretien) {
                    $entrepriseData = [
                        'id_entreprise' => $potentielEntretien['id_entreprise'],
                        'nom_entreprise' => $potentielEntretien['nom_entreprise'],
                        'dpt_entreprise' => $potentielEntretien['dpt_entreprise'],
                    ];

                    $entreprisesPotentielEntretiens[] = $entrepriseData;

                    if (isset($potentielEntretien['fichiers_offre']) && is_array($potentielEntretien['fichiers_offre'])) {
                        $fichiersOffrePotentielEntretiens[] = $potentielEntretien['fichiers_offre'];
                    } else {
                        $fichiersOffrePotentielEntretiens[] = [];
                    }
                }

                $dateActuelle = date("Y-m-d");

                $this->display(
                    'etudiants/entretiens.html.twig',
                    [
                        'dateActuelle' => $dateActuelle,
                        'formation' => $formation,
                        'etudiant' => $etudiant,
                        'entreprisesSesEntretiens' => $entreprisesSesEntretiens,
                        'fichiersOffreSesEntretiens' => $fichiersOffreSesEntretiens,
                        'entreprisesPotentielEntretiens' => $entreprisesPotentielEntretiens,
                        'fichiersOffrePotentielEntretiens' => $fichiersOffrePotentielEntretiens,
                    ]
                );
            }
        } else {
            HTTP::redirect('/');
        }
    }

    public function delete($idEntreprise, $idEtudiant)
    {

        Etudiant::getInstance()->supprimerEntretien($idEntreprise, $idEtudiant);

        HTTP::redirect("/etudiant/{$idEtudiant}");
    }

    public function add($idEntreprise, $idEtudiant)
    {

        Etudiant::getInstance()->ajouterEntretien($idEntreprise, $idEtudiant);

        HTTP::redirect("/etudiant/{$idEtudiant}");
    }

    public function profil($id)
    {
        $id = $_SESSION['user_id'];

        $etudiant = Etudiant::getInstance()->etudiantData($id);

        $this->display(
            'etudiants/profil.html.twig',
            [
                'etudiant' => $etudiant,
            ]
        );
    }
}
