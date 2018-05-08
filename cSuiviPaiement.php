<?php
/**
 * Page d'accueil de l'application web AppliFrais
 * @package default
 * @todo  RAS
 */
$repInclude = './include/';
require($repInclude . "_init.inc.php");
// page inaccessible si non connecté ou si utilisateur est un visiteur médical
if (!estUtilisateurConnecte() || $_SESSION["foncUser"] != "comptable") {
    header("Location: cAccueil.php");
}
$saisie = lireDonneePost('lsmois', "");
require($repInclude . "_entete.inc.html");
require($repInclude . "_sommaire.inc.php");
$lgMois = obtenirMoisFiche($idConnexion);
?>
<div id=contenu>
    <h2>Suivi du paiement des fiches frais</h2>
    <form action="" method="post">
        <div class="corpsForm">
            <input type="hidden" name="etape" value="validerConsult" />
            <label for="lstMois">Mois&nbsp;: </label>
            <select id="lsmois" name="lsmois" title="Selectionner un mois">
                <?php
                foreach ($lgMois as $lsmois) {
                    $mois = $lsmois['mois'];
                    $nomMois = obtenirLibelleMois(intval(substr($mois, 4, 2)));
                    $annee = intval(substr($mois, 0, 4));
                    ?>
                    <option value="<?php echo $mois; ?>"<?php if ($saisie == $mois) { ?> selected="selected"<?php } ?>><?php echo $nomMois . " " . $annee; ?></option>
                <?php } ?>
            </select>
        </div>
        <p class="piedForm">
            <input id="ok" type="submit" value="Valider" size="20"
                   title="Demandez à consulter cette fiche de frais" />
            <input id="annuler" type="reset" value="Effacer" size="20" />
        </p>
    </form>
    <?php
    if ($saisie != '') {
        ?>
        <table class ="listeLegere">
            <tr>
                <th class"qteForfait"> Nom </th>
                <th class"qteForfait"> Date </th>
                <th class"qteForfait"> Etat </th>
                <th class="action">&nbsp;</th>
            </tr>
            <?php
            $fiches = obtenirFichesValide($idConnexion, $saisie);
            foreach ($fiches as $fiche) {
                ?>
                <tr>
                    <td><?php echo $fiche['nom'] ?> <?php echo $fiche['prenom'] ?></td>
                    <td><?php echo obtenirLibelleMois(intval(substr($fiche['mois'], 4, 2))) . ' ' . substr($fiche['mois'], 0, 4) ?></td>
                    <td><?php echo obtenirLibelleEtat($idConnexion, $fiche['etat']); ?></td>
                    <td><a href='cAffichageFicheFrais.php?id=<?php echo $id ?>&amp;date=<?php echo $fiche['mois'] ?>&amp;etape=miseEnPaiement'> lien vers la fiche </a></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }
    ?>
</div>
<?php
require($repInclude . "_pied.inc.html");
require($repInclude . "_fin.inc.php");
?>