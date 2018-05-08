<?php

/** 
 * Regroupe les fonctions de gestion d'une session utilisateur.
 * 
 * @package Default
 * @todo    RAS
 */

/** 
 * Démarre ou poursuit une session.
 *
 * @return void
 */
function initSession() {
    session_start();
}

/** 
 * Fournit l'id du utilisateur connecté.
 *
 * Retourne l'id du utilisateur connecté, une chaîne vide si pas de utilisateur connecté.
 * @return string id du utilisateur connecté
 */
function obtenirIdUserConnecte() {
    $ident="";
    if ( isset($_SESSION["loginUser"]) ) {
        $ident = (isset($_SESSION["idUser"])) ? $_SESSION["idUser"] : '';   
    }  
    return $ident ;
}

/**
 * Conserve en variables session les informations du utilisateur connecté
 * 
 * Conserve en variables session l'id $id et le login $login du utilisateur connecté
 * @param string id du utilisateur
 * @param string login du utilisateur
 * @param string $fonction fonction de l'utilisateur
 * @return void    
 */
function affecterInfosConnecte($id, $login,$fonction) {
    $_SESSION["idUser"] = $id;
    $_SESSION["loginUser"] = $login;
    $_SESSION["foncUser"] = $fonction;
}

/** 
 * Déconnecte le utilisateur qui s'est identifié sur le site.                     
 *
 * @return void
 */
function deconnecterUtilisateur() {
    unset($_SESSION["idUser"]);
    unset($_SESSION["loginUser"]);
    unset($_SESSION["foncUser"]);
}

/** 
 * Vérifie si un utilisateur s'est connecté sur le site.                     
 *
 * Retourne true si un utilisateur s'est identifié sur le site, false sinon. 
 * @return boolean échec ou succès
 */
function estUtilisateurConnecte() {
    // actuellement il n'y a que les utilisateurs qui se connectent
    return isset($_SESSION["loginUser"]);
}
?>