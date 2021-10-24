<?php

/**
 * getProduct: utilise une requête SQL pour retourner:
 *     - quand il n'y a pas d'id: une liste des produits dont le titre commence par la lettre alpha
 *     - quand il y a un id: les données du produit dont l'id est égal au paramètre id
 * @param {number} id 
 * @param {character} alpha 
 * @return {data} ou {false} 
 */

function getProduct($id = null, $alpha = ""){

    if(is_null($id)){

        // création de la condition WHERE en fonctions des infos passées en paramètre
        $cond = !empty($alpha) ? "WHERE ad_title LIKE '".$alpha."%' " : "";
        $sql = "SELECT ad.ad_id, 
                        ad.ad_title, 
                        ad.ad_description, 
                        ad.ad_description_detail, 
                        ad.price, 
                        ad.price_htva, 
                        ad.amount_tva, 
                        ad.is_visible, 
                        ad.category_level_2_id,
                        ad.price_delivery, 
                        shape.shape_id, 
                        category_level_2.level_2, 
                        category_level_1.level_1, 
                        designer.designer_id, 
                        designer.firstname, 
                        designer.lastname,
                        manufacturer.manufacturer_id, 
                        manufacturer.manufacturer 
                FROM `ad`
                LEFT JOIN category_level_2 ON category_level_2.category_level_2_id = ad.category_level_2_id
                LEFT JOIN category_level_1 ON category_level_1.category_level_1_id = category_level_2.category_level_1_id
                LEFT JOIN designer ON designer.designer_id = ad.designer_id
                LEFT JOIN shape ON shape.shape_id = ad.shape_id
                LEFT JOIN manufacturer ON manufacturer.manufacturer_id = ad.manufacturer_id
                ".$cond." ORDER BY ad.ad_title;";
    }else{

        if(is_numeric($id)){
            $sql = "SELECT ad.ad_id, 
                        ad.ad_title, 
                        ad.ad_description, 
                        ad.ad_description_detail, 
                        ad.price, 
                        ad.price_htva, 
                        ad.amount_tva, 
                        ad.is_visible, 
                        ad.category_level_2_id,
                        ad.price_delivery, 
                        shape.shape_id, 
                        category_level_2.level_2, 
                        category_level_1.level_1, 
                        designer.designer_id, 
                        designer.firstname, 
                        designer.lastname,
                        manufacturer.manufacturer_id, 
                        manufacturer.manufacturer 
                    FROM `ad`
                    LEFT JOIN category_level_2 ON category_level_2.category_level_2_id = ad.category_level_2_id
                    LEFT JOIN category_level_1 ON category_level_1.category_level_1_id = category_level_2.category_level_1_id
                    LEFT JOIN designer ON designer.designer_id = ad.designer_id
                    LEFT JOIN shape ON shape.shape_id = ad.shape_id
                    LEFT JOIN manufacturer ON manufacturer.manufacturer_id = ad.manufacturer_id
                    WHERE ad.ad_id = $id;";
        }
    }

    $result = requeteResultat($sql);
    return $result;
}

/**
 * insertProduct: prend un tableau de données en paramètre pour ajouter un nouveau produit dans la DB
 * construit une requête SQL pour mettre à jour un nouveau produit, elle sera passée à ExecRequete()
 * Par défaut, $is_visible et $is_disponible sont mis à 1
 * @param {data} array 
 */

function insertProduct($data){
    $category_level_2_id = $data["category_level_2_id"];
    $admin_id = $data["admin_id"];
    $shape_id = $data["shape_id"];
    $designer_id = $data["designer_id"];
    $manufacturer_id = $data["manufacturer_id"];
    $ad_title = $data["ad_title"];
    $ad_description = $data["ad_description"];
    $ad_description_detail = $data["ad_description_detail"];
    $price = $data["price"];
    $price_htva = $data["price_htva"];
    $amount_tva = $data["amount_tva"];
    $price_delivery = $data["price_delivery"];
    $is_visible = 1;
    $is_disponible = 1;

    $sql = "INSERT INTO ad(
                category_level_2_id, 
                admin_id, 
                shape_id, 
                designer_id, 
                manufacturer_id, 
                ad_title, 
                ad_description, 
                ad_description_detail, 
                price, 
                price_htva, 
                amount_tva, 
                price_delivery, 
                is_visible, 
                is_disponible
                ) 
            VALUES(
                '$category_level_2_id', 
                '$admin_id', 
                '$shape_id', 
                '$designer_id', 
                '$manufacturer_id', 
                '$ad_title', 
                '$ad_description', 
                '$ad_description_detail', 
                '$price', 
                '$price_htva', 
                '$amount_tva', 
                '$price_delivery', 
                '$is_visible', 
                '$is_disponible'
                );
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

/**
 * updateProduct: prend un id et un tableau de données en paramètre pour mettre à jour un nouveau produit dans la DB
 * construit une requête SQL pour mettre à jour un nouveau produit, elle sera passée à ExecRequete()
 * Par défaut, $is_disponible est mis à 1
 * @param {number} $id 
 * @param {array} $data 
 */

function updateProduct($id, $data){
    if(!is_numeric((int)($id))){
        return false;
    }
    $category_level_2_id = $data["category_level_2_id"];
    $admin_id = $data["admin_id"];
    $shape_id = $data["shape_id"];
    $designer_id = $data["designer_id"];
    $manufacturer_id = $data["manufacturer_id"];
    $ad_title = $data["ad_title"];
    $ad_description = $data["ad_description"];
    $ad_description_detail = $data["ad_description_detail"];
    $price = $data["price"];
    $price_htva = $data["price_htva"];
    $amount_tva = $data["amount_tva"];
    $price_delivery = $data["price_delivery"];

    $sql = "UPDATE ad 
                SET
                category_level_2_id = '$category_level_2_id',
                admin_id = '$admin_id',
                shape_id = '$shape_id',
                designer_id = '$designer_id',
                manufacturer_id = '$manufacturer_id',
                ad_title = '$ad_title',
                ad_description = '$ad_description',
                ad_description_detail = '$ad_description_detail',
                price = '$price',
                price_htva = '$price_htva',
                amount_tva = '$amount_tva',
                price_delivery = '$price_delivery'

            WHERE ad_id = '$id';
            ";
    return ExecRequete($sql);

}

/**
 * showHideProduct: prend un id en paramètre pour changer la valeur de is_visible d'un produit dans la DB 
 * construit la requête SQL pour la passer à la fonction ExecRequete() qui l'exécutera
 * @param {number} $id 
 */

function showHideProduct($id){
    if(!is_numeric($id)){
        return false;
    }
    // récupération de l'état avant mise à jour
    $sql = "SELECT is_visible FROM ad WHERE ad_id = ".$id.";";
    $result = requeteResultat($sql);
    if(is_array($result)){
        $etat_is_visble = $result[0]["is_visible"];

        $nouvel_etat = $etat_is_visble == "1" ? "0" : "1";
        // mise à jour vers le nouvel état
        $sql = "UPDATE ad SET is_visible = '".$nouvel_etat."' WHERE ad_id = ".$id.";";
        // exécution de la requête
        return ExecRequete($sql);

    }else{
        return false;
    }
}


?>