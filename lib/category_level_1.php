<?php

/**
 * getCategoryLevel1: construit une requête SQL pour retourner:
 *     - quand il n'y a pas de paramètre id: une liste des catégories de niveau 1
 *     - quand il y a un paramètre id: les données de la catégorie de niveau 1 et d'id égale au paramètre id
 *  construit la requête SQL pour la passer à la fonction requeteResultat() qui l'exécutera
 * @param {number} $id 
 */

function getCategoryLevel1($id = null){

    if(is_null($id)){
        // création de la condition WHERE en fonctions des infos passées en paramètre
        $sql = "SELECT category_level_1_id, level_1, is_visible FROM category_level_1;";
    }else{
        if(is_numeric($id)){
            $sql = "SELECT category_level_1_id, level_1, is_visible FROM category_level_1 WHERE category_level_1_id = $id;";
        }
    }

    $result = requeteResultat($sql);
    return $result;
}

/**
 * insertCategoryLevel1: prend un tableau de données en paramètre pour ajouter une nouvelle catégorie de level 1 dans la DB
 * construit la requête SQL pour la passer à la fonction ExecRequete() qui l'exécutera
 * @param {array} $data 
 */

function insertCategoryLevel1($data){
    $level_1 = $data["level_1"];

    $sql = "INSERT INTO category_level_1
                (level_1) 
            VALUES
                ('$level_1');
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

/**
 * updateCategoryLevel1: prend un id et un tableau de données en paramètre pour mettre à jour une catégorie de level 1 dans la DB
 * construit la requête SQL pour la passer à la fonction ExecRequete() qui l'exécutera
 * @param {number} $id 
 * @param {array} $data 
 */

function updateCategoryLevel1($id, $data){
    if(!is_numeric($id)){
        return false;
    }
    $level_1 = $data["level_1"];

    $sql = "UPDATE category_level_1 
                SET
                    level_1 = '$level_1'
            WHERE category_level_1_id = $id;
            ";
    // exécution de la requête
    return ExecRequete($sql);

}

/**
 * showHideCategoryLevel1: prend un id en paramètre pour changer la valeur de is_visible d'une catégorie de level 1 dans la DB 
 * construit la requête SQL pour la passer à la fonction ExecRequete() qui l'exécutera
 * @param {number} $id 
 */

function showHideCategoryLevel1($id){
    if(!is_numeric($id)){
        return false;
    }
    // récupération de l'état avant mise à jour
    $sql = "SELECT is_visible FROM category_level_1 WHERE category_level_1_id = ".$id.";";
    $result = requeteResultat($sql);
    if(is_array($result)){
        $etat_is_visble = $result[0]["is_visible"];

        $nouvel_etat = $etat_is_visble == "1" ? "0" : "1";
        // mise à jour vers le nouvel état
        $sql = "UPDATE category_level_1 SET is_visible = '".$nouvel_etat."' WHERE category_level_1_id = ".$id.";";
        // exécution de la requête
        return ExecRequete($sql);

    }else{
        return false;
    }
}


?>