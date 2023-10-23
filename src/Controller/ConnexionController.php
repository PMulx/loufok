<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

session_start();

use App\Helper\HTTP;
use App\Model\Connexion;

class ConnexionController extends Controller
{

    public function index()
    {
        $errorMessage = null;
        $_SESSION['role'] = NULL;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['mdp'];

            $user = Connexion::getInstance()->authenticateUser($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];

                if ($user['type'] === 'etudiant') {
                    $_SESSION['role'] = 'etudiant';
                    HTTP::redirect("/etudiant/{$user['id']}");
                } elseif ($user['type'] === 'formation') {
                    $_SESSION['role'] = 'responsable';
                    HTTP::redirect("/responsable/{$user['id']}");
                }
            } else {
                $errorMessage = "Adresse e-mail ou mot de passe incorrect.";
            }
        }
        $this->display(
            'connexion/index.html.twig',
            ['errorMessage' => $errorMessage ?? null]
        );
    }

    public function disconnect()
    {
        $_SESSION['role'] = NULL;
        $_SESSION['user_id'] = NULL;

        HTTP::redirect("/");
    }
}
