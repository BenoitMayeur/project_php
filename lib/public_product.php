<?php

/**************************************************************************************************
 * getProductsPerPage: utilise une requête SQL pour retourner:
 *     - les données d'un certain nombre de produits ($amountPerPage) à partir du produit dont la position 
 *          est égale à $lower_lim
 *          => ces données sont composites de plusieurs tables
 *     - les données sont classées par ordre alphabétique sur base du titre
 *     - les produits qui sont cachés (is_visible = 0) ne sont pas inclus
 *     - les produits dont la catégorie ou la sous-catégorie sont cachés, ne sont pas inclus
 * @param {number} amountPerPage 
 * @param {number} lower_lim 

 ***************************************************************************************************
 */ 
function getProductsPerPage($amountPerPage, $lower_lim){

    $sql = "SELECT ad.ad_id, 
    ad.ad_title, 
    ad.ad_description_detail, 
    ad.price, 
    ad.is_visible, 
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
    LEFT JOIN manufacturer ON manufacturer.manufacturer_id = ad.manufacturer_id
    WHERE ad.is_visible LIKE 1
    AND category_level_1.is_visible LIKE 1
    AND category_level_2.is_visible LIKE 1
    ORDER BY ad.ad_title
    LIMIT ".$lower_lim.",".$amountPerPage.";";

    $listDesigners = requeteResultat($sql);

    return $listDesigners;
}

/**************************************************************************************************
 * getProductById: utilise une requête SQL pour retourner:
 *     - les données d'un produit sur base d'un id ($id)
 *          => ces données sont composites de plusieurs tables
 *     - MAIS on vérifie aussi que ce produit peut être montré aux clients (is_visible LIKE 1)
 *     - les produits dont la catégorie ou la sous-catégorie sont cachés, ne sont pas inclus
 * @param {number} id 

 ***************************************************************************************************
 */ 

function getProductById($id){
    $sql = "SELECT ad.ad_id, 
    ad.ad_title, 
    ad.ad_description, 
    ad.ad_description_detail, 
    ad.price, 
    ad.is_visible, 
    ad.category_level_2_id,
    ad.price_delivery, 
    category_level_2.level_2, 
    category_level_1.level_1, 
    designer.designer_id, 
    designer.firstname, 
    designer.lastname,
    manufacturer.manufacturer 
    FROM `ad`
    LEFT JOIN category_level_2 ON category_level_2.category_level_2_id = ad.category_level_2_id
    LEFT JOIN category_level_1 ON category_level_1.category_level_1_id = category_level_2.category_level_1_id
    LEFT JOIN designer ON designer.designer_id = ad.designer_id
    LEFT JOIN manufacturer ON manufacturer.manufacturer_id = ad.manufacturer_id
    WHERE ad.ad_id = ".$id." AND ad.is_visible LIKE 1
    AND category_level_1.is_visible LIKE 1
    AND category_level_2.is_visible LIKE 1;";

    $designer = requeteResultat($sql);

    return $designer;
}

/**************************************************************************************************
 * getAmountProducts: utilise une requête SQL pour retourner:
 *     - le nombre total de produits de la DB qui peuvent être montrés aux clients (is_visible LIKE 1)
       - les produits qui sont cachés (is_visible = 0) ne sont pas inclus
       - les produits dont la catégorie ou la sous-catégorie sont cachés, ne sont pas inclus
 ***************************************************************************************************
 */ 

function getAmountProducts(){
    $sql = "SELECT COUNT(ad_id) AS 'amount' 
    FROM ad
    LEFT JOIN category_level_2 ON category_level_2.category_level_2_id = ad.category_level_2_id
    LEFT JOIN category_level_1 ON category_level_1.category_level_1_id = category_level_2.category_level_1_id
    WHERE ad.is_visible LIKE 1
    AND category_level_1.is_visible LIKE 1
    AND category_level_2.is_visible LIKE 1";

    $amountProducts = requeteResultat($sql);

    return $amountProducts[0]['amount'];
}
?>