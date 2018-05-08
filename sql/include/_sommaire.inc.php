<?php
/**
 * Contient la division pour le sommaire, sujet à des variations suivant la 
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur 
 * @todo  RAS
 */
?>
<!-- Division pour le sommaire -->
<div id="menuGauche">
    <div id="infosUtil">
        <?php
        if (estUtilisateurConnecte()) {
            $idUser = obtenirIdUserConnecte();
            $lgUser = obtenirDetailUtilisateur($idConnexion, $idUser);
            $nom = $lgUser['nom'];
            $prenom = $lgUser['prenom'];
            $fonction = $lgUser['fonction'];
            ?>
            <h2>
                <?php
                echo $nom . "</br>" . $prenom;
                ?>
            </h2>
            <?php
        }
        ?>  
    </div>  
    <?php
    if (estUtilisateurConnecte() && $fonction == 'visiteur') {
        ?>
        <h3>Visiteur médical</h3>
        <ul id="menuList">
            <li class="smenu">
                <a href="cAccueil.php" title="Page d'accueil">Accueil</a>
            </li>
            <li class="smenu">
                <a href="cSaisieFicheFrais.php" title="Saisie fiche de frais du mois courant">Saisie fiche de frais</a>
            </li>
            <li class="smenu">
                <a href="cConsultFichesFrais.php" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
            </li>
            <?php
            // affichage des éventuelles erreurs déjà détectées
            if (nbErreurs($tabErreurs) > 0) {
                echo toStringErreurs($tabErreurs);
            }
        } else {
            ?>
            <h3>Comptable</h3>
            <ul id="menuList">
                <li class="smenu">
                    <a href="cAccueil.php" title="Page d'accueil">Accueil</a>
                </li>
                <li class="smenu">
                    <a href="cValidationFicheFrais.php" title="Validation des fiches de frais">Validation des fiches de frais</a>
                </li>
                <li class="smenu">
                    <a href="cSuiviPaiement.php" title="Suivie des payement">Suivie des payements</a>
                </li>
                <?php
                // affichage des éventuelles erreurs déjà détectées
                if (nbErreurs($tabErreurs) > 0) {
                    echo toStringErreurs($tabErreurs);
                }
            }
            ?>
            <li class="smenu">
                <a href="cSeDeconnecter.php" title="Se déconnecter">Se déconnecter</a>
            </li>
        </ul>
</div>
