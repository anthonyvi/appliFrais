<?php
/*
 * Script de contrôle et d'affichage du cas d'utilisation "Valider fiche de frais"
 * @package default
 * @todo  RAS
 */
$repInclude = './include/';
  require($repInclude . "_init.inc.php");
  
    // page inaccessible si visiteur non connecté  ou si utilisateur est un visiteur médical
  if(! estVisiteurConnecte() || $_SESSION["foncUser"] != "comptable"){
      header("Location: cAccueil.php");
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  $mois = filter_input(INPUT_GET,'mois');
  $utilisateur = filter_input(INPUT_GET,'utilisateur');
  $fiche = obtenirFiche($idConnexion, $mois, $utilisateur);
  $date = str_split($fiche['mois'], 4); 
  $ligneforfais = obtenirLigneForfais($idconnection, $mois, $utilisateur);
  ?>
<div id="contenu">
    <h2>Fiche <?php echo $date[1] . "/" . $date[0] . " " . $fiche['nom'] . " " . $fiche['prenom'];?> </h2>
    <h3>Frais forfait</h3>
     <table class ="listeLegere">
                <tr>
                  <th class="qteForfait">Quantité</th>
                  <th class="qteForfait">Libellé</th>
                  <th class="qteForfait">Prix unitaire</th>
                  <th class="qteForfait">Prix total</th>
                </tr>
     </table>
    <h3>Frais hors forfait</h3>
     <table class ="listeLegere">
                <tr>
                  <th class="qteForfait">Libellé</th>
                  <th class="qteForfait">Date</th>
                  <th class="qteForfait">Montant</th>
                </tr>
     </table>
</div>
<?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>