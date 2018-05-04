<?php
/*
 * Script de contrôle et d'affichage du cas d'utilisation "Valider fiche de frais"
 * @package default
 * @todo  RAS
 */
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// page inaccessible si visiteur non connecté  ou si utilisateur est un visiteur médical
if (!estVisiteurConnecte() || $_SESSION["foncUser"] != "comptable") {
    header("Location: cAccueil.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
?>
<div id="contenu">
</div>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>
