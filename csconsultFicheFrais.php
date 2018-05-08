<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$repInclude = './include/';
require($repInclude . "_init.inc.php");
if (estUtilisateurConnecte()) {
    $idUser = obtenirIdUserConnecte();
    $lgUser = obtenirDetailUtilisateur($idConnexion, $idUser);
    $fonction = $lgUser ['fonction'];
}
// page inaccessible si utilisateur non connecté  ou si utilisateur est un utilisateur médical
if (!estUtilisateurConnecte() || $fonction == 'visiteur') {
    header("Location: cAcceuil.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
