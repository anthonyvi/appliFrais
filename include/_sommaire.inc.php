<?php
/**
 * Contient la division pour le sommaire, sujet Ã  des variations suivant la 
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur 
 * @todo  RAS
 */
?>
<!-- Division pour le sommaire -->
<div id="menuGauche">
    <div id="infosUtil">
        <?php
        if (estVisiteurConnecte()) {
            $idUser = obtenirIdUserConnecte();
            $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
            $nom = $lgUser['nom'];
            $prenom = $lgUser['prenom'];
            $fonction = $_SESSION["foncUser"];
            ?>
            <h2>
                <?php
                echo $nom . " " . $prenom;
                ?>
            </h2>
            <?php
            if ($fonction == "visiteur") {
                ?>   
                <h3>Visiteur médical</h3>   
                <?php
            } else {
                ?>
                <h3>Comptable</h3>
                <?php
            }
        }
        ?>  
    </div>  
        <?php
        if (estVisiteurConnecte()) {
            ?>
        <ul id="menuList">
            <li class="smenu">
                <a href="cAccueil.php" title="Page d'accueil">Accueil</a>
            </li>
    <?php
    if ($fonction == "visiteur") {
        ?>   
                <li class="smenu">
                    <a href="cSaisieFicheFrais.php" title="Saisie fiche de frais du mois courant">Saisie fiche de frais</a>
                </li>
                <li class="smenu">
                    <a href="cConsultFichesFrais.php" title="Consultation de mes fiches de frais">Mes fiches de frais</a>
                </li>
        <?php
    } else {
        ?>
                <li class="smenu">
                    <a href="cValidationFicheFrais.php" title="Validation des fiches de frais">Validation des fiches de frais</a>
                </li>
                <li class="smenu">
                    <a href="cSuiviePaiementFicheFrais.php" title="Suivie du paiement des fiches de frais">Suivie du paiement des fiches de frais</a>
                </li>
        <?php
    }
}
?>  
        <li class="smenu">
            <a href="cSeDeconnecter.php" title="Se déconnecter">Se déconnecter</a>
        </li>

    </ul>
<?php
// affichage des éventuelles erreurs déja détectées
if (nbErreurs($tabErreurs) > 0) {
    echo toStringErreurs($tabErreurs);
}
?>
</div>
