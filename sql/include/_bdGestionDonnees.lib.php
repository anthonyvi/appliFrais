<?php

/**
 * Regroupe les fonctions d'accès aux données.
 * @package Default
 * @author Arthur Martin
 * @todo Fonctions retournant plusieurs lignes sont à réécrire.
 */

/**
 * Se connecte au serveur de données MySql.                      
 * Se connecte au serveur de données MySql à partir de valeurs
 * prédéfinies de connexion (hôte, compte utilisateur et mot de passe). 
 * Retourne l'identifiant de connexion si succès obtenu, le booléen false 
 * si problème de connexion.
 * @return resource identifiant de connexion
 */
function connecterServeurBD() {
    $hote = "localhost";
    $login = "root";
    $mdp = "";
    $dbnom = "gsb";
    return mysqli_connect($hote, $login, $mdp, $dbnom);
}

/**
 * Sélectionne (rend active) la base de données.
 * Sélectionne (rend active) la BD prédéfinie gsb_frais sur la connexion
 * identifiée par $idCnx. Retourne true si succès, false sinon.
 * @param resource $idCnx identifiant de connexion
 * @return boolean succès ou échec de sélection BD 
 */
function activerBD($idCnx) {
    $bd = "gsb";
    $query = "SET CHARACTER SET utf8";
    // Modification du jeu de caractères de la connexion
    $res = $idCnx->query($query);
    $ok = $idCnx->select_db($bd);
    return $ok;
}

/**
 * Ferme la connexion au serveur de données.
 * Ferme la connexion au serveur de données identifiée par l'identifiant de 
 * connexion $idCnx.
 * @param resource $idCnx identifiant de connexion
 * @return void  
 */
function deconnecterServeurBD($idCnx) {
    $idCnx->close();
}

/**
 * Echappe les caractères spéciaux d'une chaîne.
 * Envoie la chaîne $str échappée, càd avec les caractères considérés spéciaux
 * par MySql (tq la quote simple) précédés d'un \, ce qui annule leur effet spécial
 * @param string $idCnx Connexion base
 * @param string $str chaîne à échapper
 * @return string chaîne échappée 
 */
function filtrerChainePourBD($idCnx, $str) {
    if (!get_magic_quotes_gpc()) {
        // si la directive de configuration magic_quotes_gpc est activée dans php.ini,
        // toute chaîne reçue par get, post ou cookie est déjà échappée 
        // par conséquent, il ne faut pas échapper la chaîne une seconde fois                              
        $str = $idCnx->real_escape_string($str);
    }
    return $str;
}

/**
 * Fournit les informations sur un visiteur demandé. 
 * Retourne les informations du utilisateur d'id $unId sous la forme d'un tableau
 * associatif dont les clés sont les noms des colonnes(id, nom, prenom).
 * @param resource $idCnx identifiant de connexion
 * @param string $unId id de l'utilisateur
 * @return array  tableau associatif du utilisateur
 */
function obtenirDetailUtilisateur($idCnx, $unId) {
    $id = filtrerChainePourBD($idCnx, $unId);
    $requete = "select id, nom, prenom,fonction from utilisateur where id='" . $unId . "'";
    $idJeuRes = $idCnx->query($requete);
    $ligne = false;
    if ($idJeuRes) {
        $ligne = $idJeuRes->fetch_assoc();
        $idJeuRes->free_result();
    }
    return $ligne;
}

/**
 * Fournit les informations d'une fiche de frais. 
 * Retourne les informations de la fiche de frais du mois de $unMois (MMAAAA)
 * sous la forme d'un tableau associatif dont les clés sont les noms des colonnes
 * (nbJustitificatifs, idEtat, libelleEtat, dateModif, montantValide).
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdUtilisateur id utilisateur  
 * @return array tableau associatif de la fiche de frais
 */
function obtenirDetailFicheFrais($idCnx, $unMois, $unIdUtilisateur) {
    $unMois = filtrerChainePourBD($idCnx, $unMois);
    $ligne = false;
    $requete = "select IFNULL(nbJustificatifs,0) as nbJustificatifs, Etat.id as idEtat, libelle as libelleEtat, dateModif, montantValide 
    from FicheFrais inner join Etat on idEtat = Etat.id 
    where idUtilisateur='" . $unIdUtilisateur . "' and mois='" . $unMois . "'";
    $idJeuRes = $idCnx->query($requete);
    if ($idJeuRes) {
        $ligne = $idJeuRes->fetch_assoc();
    }
    $idJeuRes->free_result();

    return $ligne;
}

/**
 * Vérifie si une fiche de frais existe ou non. 
 * Retourne true si la fiche de frais du mois de $unMois (MMAAAA) du utilisateur 
 * $idUtilisateur existe, false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdUtilisateur id utilisateur  
 * @return booléen existence ou non de la fiche de frais
 */
function existeFicheFrais($idCnx, $unMois, $unIdUtilisateur) {
    $unMois = filtrerChainePourBD($idCnx, $unMois);
    $requete = "select idUtilisateur from FicheFrais where idUtilisateur='" . $unIdUtilisateur .
            "' and mois='" . $unMois . "'";
    $idJeuRes = $idCnx->query($requete);
    $ligne = false;
    if ($idJeuRes) {
        $ligne = $idJeuRes->fetch_assoc();
        $idJeuRes->free_result();
    }

    // si $ligne est un tableau, la fiche de frais existe, sinon elle n'exsite pas
    return is_array($ligne);
}

/**
 * Fournit le mois de la dernière fiche de frais d'un utilisateur.
 * Retourne le mois de la dernière fiche de frais du utilisateur d'id $unIdUtilisateur.
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdUtilisateur id utilisateur  
 * @return string dernier mois sous la forme AAAAMM
 */
function obtenirDernierMoisSaisi($idCnx, $unIdUtilisateur) {
    $requete = "select max(mois) as dernierMois from FicheFrais where idUtilisateur='" .
            $unIdUtilisateur . "'";
    $idJeuRes = $idCnx->query($requete);
    $dernierMois = false;
    if ($idJeuRes) {
        $ligne = $idJeuRes->fetch_assoc();
        $dernierMois = $ligne["dernierMois"];
        $idJeuRes->free_result();
    }
    return $dernierMois;
}

/**
 * Ajoute une nouvelle fiche de frais et les éléments forfaitisés associés, 
 * Ajoute la fiche de frais du mois de $unMois (MMAAAA) du utilisateur 
 * $idUtilisateur, avec les éléments forfaitisés associés dont la quantité initiale
 * est affectée à 0. Clôt éventuellement la fiche de frais précédente du utilisateur. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdUtilisateur id utilisateur  
 * @return void
 */
function ajouterFicheFrais($idCnx, $unMois, $unIdUtilisateur) {
    $unMois = filtrerChainePourBD($idCnx, $unMois);
    // modification de la derni�re fiche de frais du utilisateur
    $dernierMois = obtenirDernierMoisSaisi($idCnx, $unIdUtilisateur);
    $laDerniereFiche = obtenirDetailFicheFrais($idCnx, $dernierMois, $unIdUtilisateur);
    if (is_array($laDerniereFiche) && $laDerniereFiche['idEtat'] == 'CR') {
        modifierEtatFicheFrais($idCnx, $dernierMois, $unIdUtilisateur, 'CL');
    }

    // ajout de la fiche de frais à l'état Créé
    $requete = "insert into FicheFrais (idUtilisateur, mois, nbJustificatifs, montantValide, idEtat, dateModif) values ('"
            . $unIdUtilisateur
            . "','" . $unMois . "',0,NULL, 'CR', '" . date("Y-m-d") . "')";
    $idCnx->query($requete);

    // ajout des éléments forfaitisés
    $requete = "select id from FraisForfait";
    $idJeuRes = $idCnx->query($requete);
    if ($idJeuRes) {
        $ligne = $idJeuRes->fetch_assoc();
        while (is_array($ligne)) {
            $idFraisForfait = $ligne["id"];
            // insertion d'une ligne frais forfait dans la base
            $requete = "insert into LigneFraisForfait (idUtilisateur, mois, idFraisForfait, quantite)
                        values ('" . $unIdUtilisateur . "','" . $unMois . "','" . $idFraisForfait . "',0)";
            $idCnx->query($requete);
            // passage au frais forfait suivant
            $ligne = mysqli_fetch_assoc($idJeuRes);
        }
        $idJeuRes->free_result();
    }
}

/**
 * Retourne le texte de la requête select concernant les mois pour lesquels un 
 * utilisateur a une fiche de frais. 
 * 
 * La requête de sélection fournie permettra d'obtenir les mois (AAAAMM) pour 
 * lesquels le utilisateur $unIdUtilisateur a une fiche de frais. 
 * @param string $unIdUtilisateur id utilisateur  
 * @return string texte de la requête select
 */
function obtenirReqMoisFicheFrais($unIdUtilisateur) {
    $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idUtilisateur ='"
            . $unIdUtilisateur . "' order by fichefrais.mois desc ";
    return $req;
}

/**
 * Retourne le texte de la requête select concernant les éléments forfaitisés 
 * d'un utilisateur pour un mois donnés. 
 * 
 * La requête de sélection fournie permettra d'obtenir l'id, le libellé et la
 * quantité des éléments forfaitisés de la fiche de frais du utilisateur
 * d'id $idUtilisateur pour le mois $mois
 * @param string $idCnx Connexion base
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdUtilisateur id utilisateur  
 * @return string texte de la requête select
 */
function obtenirReqEltsForfaitFicheFrais($idCnx, $unMois, $unIdUtilisateur) {
    $unMois = filtrerChainePourBD($idCnx, $unMois);
    $requete = "select idFraisForfait, libelle, quantite from LigneFraisForfait
              inner join FraisForfait on FraisForfait.id = LigneFraisForfait.idFraisForfait
              where idUtilisateur='" . $unIdUtilisateur . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * Retourne le texte de la requête select concernant les éléments hors forfait 
 * d'un utilisateur pour un mois donnés. 
 * 
 * La requête de sélection fournie permettra d'obtenir l'id, la date, le libellé 
 * et le montant des éléments hors forfait de la fiche de frais du utilisateur
 * d'id $idUtilisateur pour le mois $mois    
 * @param string $idCnx Connexion base
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdUtilisateur id utilisateur  
 * @return string texte de la requête select
 */
function obtenirReqEltsHorsForfaitFicheFrais($idCnx, $unMois, $unIdUtilisateur) {
    $unMois = filtrerChainePourBD($idCnx, $unMois);
    $requete = "select id, date, libelle, montant from LigneFraisHorsForfait
              where idUtilisateur='" . $unIdUtilisateur
            . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * 
 * @param string $idCnx
 * @param string $unMois
 * @param string $unIdUtilisateur
 * @return array
 */
function obtenirNbJustificatif($idCnx, $unMois, $unIdUtilisateur) {
    $requete = "select nbJustificatifs from fichefrais
              where idUtilisateur='" . $unIdUtilisateur . "' and mois='" . $unMois . "'";
    $idJeuxNb = $idCnx->query($requete);
    $nbarrray = $idJeuxNb->fetch_assoc();
    $nb = $nbarrray['nbJustificatifs'];
    $idJeuxNb->free_result();

    return $nb;
}

/**
 * Supprime une ligne hors forfait.
 * Supprime dans la BD la ligne hors forfait d'id $unIdLigneHF
 * @param resource $idCnx identifiant de connexion
 * @param string $idCnx Connexion base
 * @param string $idLigneHF id de la ligne hors forfait
 * @return void
 */
function supprimerLigneHF($idCnx, $unIdLigneHF) {
    $requete = "delete from LigneFraisHorsForfait where id = " . $unIdLigneHF;
    $idCnx->query($requete);
}

/**
 * Ajoute une nouvelle ligne hors forfait.
 * Insère dans la BD la ligne hors forfait de libellé $unLibelleHF du montant 
 * $unMontantHF ayant eu lieu à la date $uneDateHF pour la fiche de frais du mois
 * $unMois du utilisateur d'id $unIdUtilisateur
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (AAMMMM)
 * @param string $unIdUtilisateur id du utilisateur
 * @param string $uneDateHF date du frais hors forfait
 * @param string $unLibelleHF libellé du frais hors forfait 
 * @param double $unMontantHF montant du frais hors forfait
 * @return void
 */
function ajouterLigneHF($idCnx, $unMois, $unIdUtilisateur, $uneDateHF, $unLibelleHF, $unMontantHF) {
    $unLibelleHF = filtrerChainePourBD($idCnx, $unLibelleHF);
    $uneDateHF = filtrerChainePourBD($idCnx, convertirDateFrancaisVersAnglais($uneDateHF));
    $unMois = filtrerChainePourBD($idCnx, $unMois);
    $requete = "insert into LigneFraisHorsForfait(idUtilisateur, mois, date, libelle, montant) 
                values ('" . $unIdUtilisateur . "','" . $unMois . "','" . $uneDateHF . "','" . $unLibelleHF . "'," . $unMontantHF . ")";
    $idCnx->query($requete);
}

/**
 * Modifie les quantités des éléments forfaitisés d'une fiche de frais. 
 * Met à jour les éléments forfaitisés contenus  
 * dans $desEltsForfaits pour le utilisateur $unIdUtilisateur et
 * le mois $unMois dans la table LigneFraisForfait, après avoir filtré 
 * (annulé l'effet de certains caractères considérés comme spéciaux par 
 *  MySql) chaque donnée   
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA) 
 * @param string $unIdUtilisateur  id utilisateur
 * @param array $desEltsForfait tableau des quantités des éléments hors forfait
 * avec pour clés les identifiants des frais forfaitisés 
 * @return void 
 */
function modifierEltsForfait($idCnx, $unMois, $unIdUtilisateur, $desEltsForfait) {
    $unMois = filtrerChainePourBD($idCnx, $unMois);
    $unIdUtilisateur = filtrerChainePourBD($idCnx, $unIdUtilisateur);
    foreach ($desEltsForfait as $idFraisForfait => $quantite) {
        $requete = "update LigneFraisForfait set quantite = " . $quantite
                . " where idUtilisateur = '" . $unIdUtilisateur . "' and mois = '"
                . $unMois . "' and idFraisForfait='" . $idFraisForfait . "'";
        $idCnx->query($requete);
    }
}

/**
 * 
 * @param strring $idCnx
 * @param string $mois
 * @param string $utilisateur
 * @param string $nbjus
 */
function validerFicheFrais($idCnx, $mois, $utilisateur, $nbjus) {
    var_dump($nbjus);
    $requete = "UPDATE fichefrais
                SET nbJustificatifs = $nbjus, idEtat = 'VA'
                WHERE idUtilisateur = '$utilisateur' AND mois = '$mois'";
    $idCnx->query($requete);
    $idCnx->close;
}

/**
 * Contrôle les informations de connexionn d'un utilisateur.
 * Vérifie si les informations de connexion $unLogin, $unMdp sont ou non valides.
 * Retourne les informations de l'utilisateur sous forme de tableau associatif 
 * dont les clés sont les noms des colonnes (id, nom, prenom, login, mdp)
 * si login et mot de passe existent, le booléen false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unLogin login 
 * @param string $unMdp mot de passe 
 * @return array tableau associatif ou booléen false 
 */
function verifierInfosConnexion($idCnx, $unLogin, $unMdp) {
    $unLogin = filtrerChainePourBD($idCnx, $unLogin);
    $unMdp = filtrerChainePourBD($idCnx, $unMdp);
    // le mot de passe est crypté dans la base avec la fonction de hachage md5
    $req = "select id, nom, prenom, login, mdp, fonction from Utilisateur where login='" . $unLogin . "' and mdp='" . $unMdp . "'";
    $idJeuRes = $idCnx->query($req);
    $ligne = false;
    if ($idJeuRes) {
        $ligne = $idJeuRes->fetch_assoc();
        $idJeuRes->free_result();
    }
    return $ligne;
}

/**
 * Modifie l'état et la date de modification d'une fiche de frais

 * Met à jour l'état de la fiche de frais du utilisateur $unIdUtilisateur pour
 * le mois $unMois à la nouvelle valeur $unEtat et passe la date de modif à 
 * la date d'aujourd'hui
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdUtilisateur 
 * @param string $unMois mois sous la forme aaaamm
 * @param string $unEtat 
 * @return void 
 */
function modifierEtatFicheFrais($idCnx, $unMois, $unIdUtilisateur, $unEtat) {
    $requete = "update FicheFrais set idEtat = '" . $unEtat .
            "', dateModif = now() where idUtilisateur ='" .
            $unIdUtilisateur . "' and mois = '" . $unMois . "'";
    $idCnx->query($requete);
}

/**
 * 
 * @param string $idCnx
 * @return array
 */
function obtenirFiches($idCnx) {
    $requete = "SELECT id, nom, prenom, idEtat, idUtilisateur, mois
                FROM utilisateur
                INNER JOIN FicheFrais ON utilisateur.id = FicheFrais.idUtilisateur
                WHERE idEtat = 'CL'
                GROUP BY nom";
    $idJeuRes = $idCnx->query($requete);
    $fiches = false;
    if ($idJeuRes) {
        while ($fiche = $idJeuRes->fetch_assoc()) {
            $fiches [] = array(
                'id' => $fiche['id'],
                'nom' => $fiche['nom'],
                'prenom' => $fiche['prenom'],
                'etat' => $fiche['idEtat'],
                'utilisateur' => $fiche['idUtilisateur'],
                'mois' => $fiche['mois']
            );
        }
        $idJeuRes->free_result();
    }
    return $fiches;
}

function obtenirFiche($idCnx, $date, $util) {
    $requete = "SELECT idUtilisateur, mois, nbJustificatifs, montantValide, dateModif, idEtat, nom,prenom
             FROM utilisateur
             INNER JOIN fichefrais ON utilisateur.id = fichefrais.idUtilisateur
             WHERE idUtilisateur = '$util' and mois = '$date'";

    $idJeuRes = $idCnx->query($requete);
    if ($idJeuRes) {
        $fiche = $idJeuRes->fetch_assoc();
    }
    $idJeuRes->free_result();
    return $fiche;
}

function ligneForfais($idCnx, $date, $util) {
    $requete = "SELECT idUtilisateur, mois, quantite, libelle, montant
                FROM lignefraisforfait
                INNER JOIN fraisforfait ON lignefraisforfait.idFraisForfait = fraisforfait.id
                WHERE idUtilisateur = '$util' and mois = '$date'";
    $idJeuRes = $idCnx->query($requete);
    $lignes = false;
    if ($idJeuRes) {
        while ($ligne = $idJeuRes->fetch_assoc()) {
            $lignes [] = array(
                'id' => $ligne['idUtilisateur'],
                'mois' => $ligne['mois'],
                'quantite' => $ligne['quantite'],
                'libelle' => $ligne['libelle'],
                'montant' => $ligne['montant']
            );
        }
        $idJeuRes->free_result();
    }
    return $lignes;
}

function horsForfait($idCnx, $date, $util) {
    $requete = "SELECT libelle, date, montant
                FROM lignefraishorsforfait
                WHERE idUtilisateur = '$util' and mois = '$date'";
    $idJeuRes = $idCnx->query($requete);
    $lignes = false;
    if ($idJeuRes) {
        while ($ligne = $idJeuRes->fetch_assoc()) {
            $lignes [] = array(
                'montant' => $ligne['montant'],
                'libelle' => $ligne['libelle'],
                'date' => $ligne['date']
            );
        }
        $idJeuRes->free_result();
    }
    return $lignes;
}

function obtenirMoisFiche($idCnx) {
    $requete = "SELECT DISTINCT  mois FROM ficheFrais 
                WHERE idEtat = 'VA'";
    $idJeuRes = $idCnx->query($requete);
    if ($idJeuRes) {
        while ($ligne = $idJeuRes->fetch_assoc()) {
            $lgMois [] = array(
                'mois' => $ligne['mois'],
            );
        }
        $idJeuRes->free_result();
    }
    return $lgMois;
}

function obtenirFichesValide($idCnx, $unMois) {
    $requete = " SELECT id, nom, prenom, mois, idEtat
            FROM fichefrais
            INNER JOIN utilisateur ON FicheFrais.idUtilisateur = utilisateur.id
            WHERE idEtat = 'VA' AND mois='$unMois'
             OR idEtat = 'MP' AND mois='$unMois'
             ORDER BY nom ";

    $idJeuRes = $idCnx->query($requete);
    $fiches = false;
    if ($idJeuRes) {
        while ($fiche = $idJeuRes->fetch_assoc()) {
            $fiches[] = array(
                'id' => $fiche['id'],
                'nom' => $fiche['nom'],
                'prenom' => $fiche['prenom'],
                'mois' => $fiche['mois'],
                'etat' => $fiche['idEtat']
            );
        }
         $idJeuRes -> free_result();
    }
    return $fiches;
}

?>
