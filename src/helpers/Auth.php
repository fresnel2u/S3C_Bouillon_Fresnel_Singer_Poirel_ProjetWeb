<?php

namespace Whishlist\Helpers;

use Exception;
use Whishlist\Models\User;

/**
 * Classe permettant de gérer les actions concernant la connexion/l'inscription des utilisateurs
 */
class Auth
{
    /**
     * Vérifie si un utilisateur est déjà connect", le connecte si les informations sont correctes et enregistre les informations 
     * nécessaires dans une variable de session
     *
     * @param string $identifier nom d'utilisateur (email)
     * @param string $password mot de passe 
     * @return void 
     */
    public static function attempt(string $identifier, string $password)
    {
        if (Auth::isLogged()) {
            throw new Exception("Vous êtes déjà connecté.");
        }

        $user = User::where('email', '=', $identifier)->firstOrFail();

        if (!password_verify($password, $user->password)) {
            throw new Exception('Nom d\'utilisateur ou mot de Passe incorrect.');
        } else {
            $_SESSION['user'] = $user->toArray();
        }
    }

    /**
     * Définie l'utilisateur courrant
     *
     * @param User $user
     * @return void
     */
    public static function setUser(?User $user)
    {
        if($user === null) {
            $_SESSION['user'] = null;
            echo("salut");
        } else {
            $_SESSION['user'] = $user->toArray();
            echo("saluta");

        }
    }

    /**
     * Vérifie que les informations entrees par l'utilisateur correspondent aux exigences attendues
     *
     * @param string $email
     * @param string $pass
     * @param string $passConfirm
     * @return void
     */
    public static function checkData(string $email, string $pass, string $passConfirm)
    {
        if (User::where('email', '=', $email)->exists()) {
            throw new Exception("Cette email est déjà utilisé.");
        } else if ($pass !== $passConfirm) {
            throw new Exception("Les deux mots de passe ne sont pas identique.");
        }
    }

    /**
     * Crée une ligne User dans la base de données lors de l'inscription d'un utilisateur
     *
     * @param string $name
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @return void
     */
    public static function createUser(string $firstname, string $lastname, string $email, string $password)
    {
        $user = new User();
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->save();
    }

    /**
     * Vérifie si un utilisateur est connecté
     *
     * @return boolean si l'utilisateur est connecté ou non
     */
    public static function isLogged(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Trouve l'utilisateur connecté
     * 
     * @return array|null l'utilisateur connecté
     */
    public static function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}
