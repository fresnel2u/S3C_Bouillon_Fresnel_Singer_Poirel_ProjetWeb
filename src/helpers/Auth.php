<?php

namespace Whishlist\Helpers;

use Exception;
use Whishlist\Models\User;

/**
 * Classe permettant de gerer les actions concernant la connexion/l'inscription des utilisateurs
 */
class Auth
{
    /**
     * Verifie si un utilisateur est deja connecte, le connecte si les informations sont correctes et enregistre les informations 
     * necessaires dans une variable de session.
     *
     * @param string $identifiers nom d'utilisateur (email)
     * @param string $password mot de passe 
     * @return void 
     */
    public static function attempt(string $identifiers, string $password): void
    {
        if (Auth::isLogged()) {
            throw new Exception("Vous êtes déjà connecté.");
        }

        $user = User::where('email', '=', $identifiers)->firstOrFail();

        if (!password_verify($password, $user->password)) {
            throw new Exception('Nom d\'utilisateur ou mot de Passe incorrect.');
        } else {
            $_SESSION['user'] = $user->toArray();
        }
    }

    /**
     * Verifie que les informations entrees par l'utilisateur correspondent aux exigences attendues
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
     * Creer une ligne User dans la base de donnees lors de l'inscription d'un utilisateur
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
     * Verifie si un utilisateur est connecte
     *
     * @return boolean - true si l'utilisateur est connecte
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
