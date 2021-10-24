<?php
/**
 * getCategoryLevel2: utilise une requête SQL pour retourner:
 *     - quand il n'y a pas de paramètre: une liste des catégories de niveau 2 quand il n'y a pas de paramètre
 *     - quand il y a un paramètre: les données de la catégorie de niveau 2 et d'id égale au paramètre
 *  construit la requête SQL pour la passer à la fonction requeteResultat() qui l'exécutera
 * @param {number} $id 
 */
function getCategoryLevel2($id = null){

    if(is_null($id)){
        // création de la condition WHERE en fonctions des infos passées en paramètre

        $sql = "SELECT 
                category_level_2.category_level_2_id,
                category_level_2.category_level_1_id,
                category_level_2.level_2,
                category_level_2.is_visible AS cat_2_is_visible,
                category_level_1.level_1,
                category_level_1.level_1 AS cat_1_is_visible
                FROM `category_level_2` 
                LEFT JOIN category_level_1 ON category_level_1.category_level_1_id = category_level_2.category_level_1_id
                ORDER BY category_level_1.level_1, category_level_2.level_2;";
    }else{
        if(is_numeric($id)){
            $sql = "SELECT 
                    category_level_2.category_level_2_id,
                    category_level_2.category_level_1_id,
                    category_level_2.level_2,
                    category_level_2.is_visible AS cat_2_is_visible,
                    category_level_1.level_1,
                    category_level_1.level_1 AS cat_1_is_visible
                    FROM `category_level_2` 
                    LEFT JOIN category_level_1 ON category_level_1.category_level_1_id = category_level_2.category_level_1_id
                    WHERE category_level_2.category_level_2_id = $id;";
        }
    }

    $result = requeteResultat($sql);
    return $result;
}

/**
 * insertCategoryLevel2: prend un tableau de données en paramètre pour ajouter une nouvelle catégorie de level 2 dans la DB
 * construit la requête SQL pour la passer à la fonction ExecRequete() qui l'exécutera
 * @param {array} $data 
 */

function insertCategoryLevel2($data){
    $level_2 = $data["level_2"];
    $category_level_1_id = $data["level_1"];

    $sql = "INSERT INTO category_level_2
                (level_2, category_level_1_id) 
            VALUES
                ('$level_2', '$category_level_1_id');
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

/**
 * updateCategoryLevel2: prend un id et un tableau de données en paramètre pour mettre à jour une catégorie de level 2 dans la DB
 * construit la requête SQL pour la passer à la fonction ExecRequete() qui l'exécutera
 * @param {number} $id 
 * @param {array} $data 
 */

function updateCategoryLevel2($id, $data){
    if(!is_numeric((int)$id)){
        return false;
    }
    $level_2 = $data["level_2"];
    $category_level_1_id = $data["category_level_1_id"];
    $sql = "UPDATE category_level_2 
                SET
                    level_2 = '$level_2',
                    category_level_1_id = '$category_level_1_id'
            WHERE category_level_2_id = '$id';
            ";
    // exécution de la requête
    return ExecRequete($sql);

}

/**
 * showHideCategoryLevel2: prend un id en paramètre pour changer la valeur de is_visible d'une catégorie de level 2 dans la DB 
 * construit la requête SQL pour la passer à la fonction ExecRequete() qui l'exécutera
 * @param {number} $id 
 */

function showHideCategoryLevel2($id){
    if(!is_numeric($id)){
        return false;
    }
    // récupération de l'état avant mise à jour
    $sql = "SELECT is_visible FROM category_level_2 WHERE category_level_2_id = ".$id.";";
    $result = requeteResultat($sql);
    if(is_array($result)){
        $etat_is_visble = $result[0]["is_visible"];

        $nouvel_etat = $etat_is_visble == "1" ? "0" : "1";
        // mise à jour vers le nouvel état
        $sql = "UPDATE category_level_2 SET is_visible = '".$nouvel_etat."' WHERE category_level_2_id = ".$id.";";
        // exécution de la requête
        return ExecRequete($sql);

    }else{
        return false;
    }
}


?>