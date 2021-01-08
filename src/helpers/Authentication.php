<?php
namespace Whishlist\helpers;

if(session_status() == PHP_SESSION_NONE)
    session_start();

use Whishlist\modele\User;
use Exception;

/**
 * Classe permettant de gerer les actions concernant la connexion/l'inscription des utilisateurs
 */
class Authentication 
{   

    /**
     * Verifie si un utilisateur est deja connecte, le connecte si les informations sont correctes et enregistre les informations 
     * necessaires dans une variable de session.
     *
     * @param string $u nom d'utilisateur (email)
     * @param string $p mot de passe 
     * @return void 
     */
    public static function Authenticate(string $u, string $p) : void
    {   
        if(Authentication::is_logged()) 
            throw new Exception("Vous êtes déjà connecté.");
        
        $user = User::where('mail', '=', $u)->firstOrFail();
        if (!password_verify($p, $user->password)) throw new Exception('Nom d\'utilisateur ou mot de Passe incorrect.');
        $_SESSION['user'] = $user;

    }

    /**
     * Verifie que les informations entrees par l'utilisateur correspondent aux exigences attendues
     *
     * @param string $email
     * @param string $pass
     * @param string $passConfirm
     * @return void
     */
    public static function CheckData(string $email, string $pass, string $passConfirm)
    {
        if (User::where('mail', '=', $email)->exists()) throw new Exception("Cette email est déjà utilisé.");
        if ($pass != $passConfirm) throw new Exception("Les deux mots de passe ne sont pas identique.");
    }

    /**
     * Creer une ligne User dans la base de donnees lors de l'inscription d'un utilisateur
     *
     * @param string $name
     * @param string $lastname
     * @param string $email
     * @param string $pass
     * @return void
     */
    public static function CreateUser(string $name, string $lastname, string $email, string $pass)
    {
        $user = new User();
        $user->nom = $name;
        $user->prenom = $lastname;
        $user->mail = $email;
        $user->password = password_hash($pass, PASSWORD_DEFAULT);
        $user->save();
    }

    /**
     * Verifie si un utilisateur est connecte
     *
     * @return boolean - true si l'utilisateur est connecte
     */
    public static function is_logged() : bool 
    {
        if(isset($_SESSION['user']))
            return true;
        return false;
    }

    /**
     * Trouve l'utilisateur connecté
     * 
     * @return User|null l'utilisateur connecté
     */
    public static function getUser() : ?User
    {
        return $_SESSION['user'] ?? null;
    }
}
