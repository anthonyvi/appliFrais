<?php


/*
 * Script de contrôle et d'affichage du cas d'utilisation "Valider fiche de frais"
 * @package default
 * @todo  RAS
 */
$repInclude = './include/';
require($repInclude . "_init.inc.php");

// page inaccessible si visiteur non connecté  ou si utilisateur est un visiteur médical
if (!estUtilisateurConnecte() || $_SESSION["foncUser"] != "comptable") {
    header("Location: cAccueil.php");
}
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
$mois = lireDonneeUrl('mois',"");
$utilisateur = lireDonneeUrl('utilisateur', "");
$fiche = obtenirFiche($idConnexion, $mois, $utilisateur);
$date = str_split($fiche['mois'], 4);
$datemodif = date('d/m/Y',strtotime($fiche['dateModif']));
$lignes = ligneForfais($idConnexion, $mois, $utilisateur);
$horsforfait = horsforfait($idConnexion, $mois, $utilisateur);
$etape=lireDonneePost("etape","attenteSaisie");
$tabErr = array();

?>
<div id="contenu">
    <h2>Fiche <?php echo $date[1] . "/" . $date[0] . " " . $fiche['nom'] . " " . $fiche['prenom'] . " " . $datemodif; ?> </h2>
    <h3>Frais forfait</h3>
    <table class ="listeLegere">
        <tr>
            <th class="qteForfait">Quantité</th>
            <th class="qteForfait">Libellé</th>
            <th class="qteForfait">Prix unitaire</th>
            <th class="qteForfait">Prix total</th>
        </tr>
        <?php foreach ($lignes as $ligne){
                 ?>
        <tr>
            <td><?php echo $ligne['quantite'];?></td>
            <td><?php echo $ligne['libelle'];?></td>
            <td><?php echo $ligne['montant'];?></td>
            <td><?php echo $ligne['quantite'] * $ligne['montant'];?></td>
        </tr>
            <?php }?>
        <tr>
            <td colspan="2"></td>
            <th>Total</th>
            <td><?php
            $total = 0;
            foreach ($lignes as $ligne){
                $total += $ligne['montant']*$ligne['quantite'];
            }
            echo $total;
?></td>
        </tr>
    </table>
    <h3>Frais hors forfait</h3>
    <table class ="listeLegere">
        <tr>
            <th class="qteForfait">Libellé</th>
            <th class="qteForfait">Date</th>
            <th class="qteForfait">Montant</th>
        </tr>
                <?php if ($horsforfait){foreach ($horsforfait as $ligne){
                 ?>
        <tr>
            <td><?php echo $ligne['libelle'];?></td>
            <td><?php echo $ligne['date'];?></td>
            <td><?php echo $ligne['montant'];?></td>
        </tr>
                <?php }}?>
        <tr><?php if ($horsforfait):?>
            <td></td>
            <th>Total</th>
            <td><?php
            $totalhors = 0;
            foreach ($horsforfait as $ligne){
                $totalhors += $ligne['montant'];
            }
            echo $totalhors;
        endif;
?>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <th> Total fiche</th>
            <td><?php if ($horsforfait) {echo $total + $totalhors;}else{ echo $total;}?></td>
        </tr>
    </table>
        <?php
    $nbdep = obtenirNbJustificatif($idConnexion,$mois, $utilisateur);
  if ($etape == "validerSaisie" ) {
      if (nbErreurs($tabErr) > 0) {
          echo toStringErreurs($tabErr);
      } 
      else {
?>
      <p class="info">Les modifications de la fiche de frais ont bien été enregistrées</p>        
<?php
      }   
  }
      ?>
     <form action="" method="post" class="corpsForm">
         <div class="corpsForm">
        <input type="hidden" name="etape" value="validerSaisie" />
        <label for="nbJus">Justificatifs&nbsp;: </label>
        <input type="text" id="nbJus"
               size ="2"
               name="nbjus" 
               title="Entrez le nombre de justificatis" 
               value="<?php echo $nbdep ?>" />
         </div>
        <p class="piedForm">
            <input id="ok" type="submit" value="Valider" size="20" 
                   title="Valider la fiche" />
            <input id="annuler" type="reset" value="Effacer" size="20" />
        </p>
    </form>
    <?php 
    $nb = lireDonneePost("nbjus", filter_input(INPUT_POST, 'nbjus'));
    if(filter_input(INPUT_POST, 'nbjus')){
    $ok = verifierEntiersPositifs(array($nb));
    if(!$ok){
        ajouterErreur($tabErr, "Le nombre de justificatifs doit être superieur à zéro");
        echo toStringErreurs($tabErr);
    }
 else {
        validerFicheFrais($idConnexion, $mois, $utilisateur, $nb);
        header('Location: cValidationFicheFrais.php?message=validee');
    }
}
?>
</div>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>