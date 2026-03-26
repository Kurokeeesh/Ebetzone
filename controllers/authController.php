<?php

require_once "models/pdoModel.php";
require_once 'models/userModel.php';
require_once "controllers/utilities.php";


class AuthController {
    private $userModel; // propriété à déclarer

    public function __construct() {
        $this->userModel = new UserModel(); // initialisation correcte
    }

    public function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->login($login, $password);

            if ($user) {
                startSessionIfNeeded();
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'login' => $user['login'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'points' => (int) $user['points'],
                ];
                setFlashMessage('success', 'Connexion reussie. Bon retour sur eBetZone.');
                redirectTo('home');
            } else {
                setFlashMessage('error', 'Login ou mot de passe incorrect.');
                redirectTo('connexion');
            }
        }
    }

    public function registerUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $login === '' || $password === '') {
            setFlashMessage('error', 'Tous les champs d inscription sont obligatoires.');
            redirectTo('connexion');
        }

        if (strlen($password) < 4) {
            setFlashMessage('error', 'Le mot de passe doit contenir au moins 4 caracteres.');
            redirectTo('connexion');
        }

        if ($this->userModel->getByLogin($login)) {
            setFlashMessage('error', 'Ce login est deja utilise.');
            redirectTo('connexion');
        }

        if ($this->userModel->getByUsername($username)) {
            setFlashMessage('error', 'Ce nom d utilisateur est deja utilise.');
            redirectTo('connexion');
        }

        $this->userModel->createUser($username, $login, $password);
        setFlashMessage('success', 'Compte cree avec succes. Vous pouvez maintenant vous connecter.');
        redirectTo('connexion');
    }
}
