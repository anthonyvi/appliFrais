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
$mois = filter_input(INPUT_GET, 'mois');
$utilisateur = filter_input(INPUT_GET, 'utilisateur');
$fiche = obtenirFiche($idConnexion, $mois, $utilisateur);
$date = str_split($fiche['mois'], 4);
$datemodif = strtotime($fiche['dateModif']);
$datemodif = date('d/m/Y', $datemodif);
$lignes = ligneForfais($idConnexion, $mois, $utilisateur);
$horsforfait = horsForfait($idConnexion, $mois, $utilisateur);
// acquisition de l'étape du traitement 
$etape = lireDonnee("etape", "demanderSaisie");
// acquisition des quantités des éléments forfaitisés 
$tabQteEltsForfait = lireDonneePost("txtEltsForfait", "");
// structure de décision sur les différentes étapes du cas d'utilisation
  if ($etape == "validerSaisie") { 
      // l'utilisateur valide les éléments forfaitisés         
      // vérification des quantités des éléments forfaitisés
      $ok = verifierEntiersPositifs($tabQteEltsForfait);      
      if (!$ok) {
          ajouterErreur($tabErreurs, "Chaque quantité doit être renseignée et numérique positive.");
      }
      else { // mise à jour des quantités des éléments forfaitisés
          modifFicheFrais($idConnexion, $mois, $utilisateur,$tabQteEltsForfait);
      }
  }                                                       
                 
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
        <?php foreach ($lignes as $ligne) {
            ?>
            <tr>
                <td><?php echo $ligne['quantite']; ?></td>
                <td><?php echo $ligne['libelle']; ?></td>
                <td><?php echo $ligne['montant']; ?></td>
                <td><?php echo $ligne['quantite'] * $ligne['montant']; ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="2"></td>
            <th>Total</th>
            <td><?php
                $total = 0;
                foreach ($lignes as $ligne) {
                    $total += $ligne['montant'] * $ligne['quantite'];
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
        <?php
        if ($horsforfait) {
            foreach ($horsforfait as $ligne) {
                ?>
                <tr>
                    <td><?php echo $ligne['libelle']; ?></td>
                    <td><?php echo $ligne['date']; ?></td>
                    <td><?php echo $ligne['montant']; ?></td>
                </tr>
            <?php }
        }
        ?>
        <tr><?php if ($horsforfait): ?>
                <td></td>
                <th>Total</th>
                <td><?php
                    $totalhors = 0;
                    foreach ($horsforfait as $ligne) {
                        $totalhors += $ligne['montant'];
                    }
                    echo $totalhors;
                endif;
                ?>
            </td>
        </tr>
    </table>
    <h3>
        Total fiche&nbsp;: <?php echo $total + $totalhors ?>
    </h3>
    <?php
    $req = obtenirNbJustificatif($mois, $utilisateur);
    $idJeunbJustificatifs = $idConnexion->query($req);
    echo $idConnexion->error;
    $res = $idJeunbJustificatifs->fetch_assoc();
  if ($etape == "validerSaisie" ) {
      if (nbErreurs($tabErreurs) > 0) {
          echo toStringErreurs($tabErreurs);
      } 
      else {
?>
      <p class="info">Les modifications de la fiche de frais ont bien été enregistrées</p>        
<?php
      }   
  }
      ?> 
    <form action="" method="post" class="corpsForm">
        <input type="hidden" name="etape" value="validerSaisie" />
        <label for="nbJustificatif">Justificatifs&nbsp;: </label>
        <input type="text" id="nbJustificatifs"
               size ="2"
               name="txtEltsForfait[<?php echo $res['nbJustificatifs'] ?>]" 
               title="Entrez le nombre de justificatis" 
               value="<?php echo $res['nbJustificatifs'] ?>" />
               <?php
               $lgEltForfait = $idJeunbJustificatifs->fetch_assoc();
               $idJeunbJustificatifs -> free_result();
               ?>
    </form>
    <p class="piedForm">
        <input id="ok" type="submit" value="Valider" size="20" 
               title="Enregistrer les nouvelles valeurs des éléments forfaitisés" />
        <input id="annuler" type="reset" value="Effacer" size="20" />
    </p> 
</div>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>