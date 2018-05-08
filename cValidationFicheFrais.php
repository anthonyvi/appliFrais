<?php
/*
 * Script de contrôle et d'affichage du cas d'utilisation "Valider fiche de frais"
 * @package Default
 * @todo  RAS
 */
$repInclude = './include/';
require($repInclude . "_init.inc.php");
   // page inaccessible si visiteur non connecté  ou si utilisateur est un visiteur médical
  if(!estUtilisateurConnecte() || $_SESSION["foncUser"] != "comptable"){
      header("Location: cAccueil.php");
  }
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
$fiches = obtenirFiches($idConnexion);
$message = lireDonneeUrl('message', "");
?>
<div id=contenu>
    <?php
    if($message == "validee"){
        ?>
    <p class="info">Fiche validée</p>
    <?php
    }
    ?>
    <h2> Validation des fiches de frais </h2>
    <?php
    if (!empty($fiches)) {
        ?>
        <table class ="listeLegere">
            <tr>
                <th class="qteForfait">Nom</th>
                <th class="qteForfait">Prenom</th>
                <th class="qteForfait">Fiche de frais</th>
                <th class="qteForfait">Etat</th>
            </tr>
            <?php foreach ($fiches as $fiche) { ?>
                <tr>
                    <td><?php echo $fiche['nom']?></td>
                    <td><?php echo $fiche['prenom']?></td>
                    <td><a href="cAffichageFiche.php?mois=<?php echo $fiche['mois']?>&utilisateur=<?php echo $fiche['utilisateur']?>"> lien vers la fiche </a></td>
                    <td><?php echo $fiche['etat'];?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php } else {
        ?>
        <p class=info> Aucune fiche a valider </p>
        <?php
    }
    ?>

</div>
<?php
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>


