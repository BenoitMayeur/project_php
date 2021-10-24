<?php
adminProtection();
include_once("lib/product.php");
include_once("lib/category_level_1.php");
include_once("lib/category_level_2.php");
include_once("lib/designer.php");
include_once("lib/manufacturer.php");
include_once("lib/shape.php");

$url_page = "gestion_product";

$taux_tva = 0.21;

$get_action = isset($_GET["action"]) ? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) : "liste";
$get_id     = isset($_GET["id"])    ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS)      : null;
$get_alpha  = isset($_GET["alpha"]) ? filter_input(INPUT_GET, 'alpha', FILTER_SANITIZE_SPECIAL_CHARS)   : "A";

/*
    En fonction du paramètre $get_action, on affichera:
        - la liste des produits
        - le formulaire d'ajout d'un produit
        - le formulaire de mise à jour d'un produit
        Ou on mettra à jour le statut "visible" d'un produit
*/

switch($get_action){
    case "liste": // liste des produits
        $page_view = "product_liste";
        $alphabet = range('A', 'Z');
        $result = getProduct(null, $get_alpha);

        if(!is_null($get_id) && is_numeric($get_id)){
            $result_detail = getProduct($get_id);

            $detail_ad_title        = $result_detail[0]["ad_title"];
            $detail_ad_description            = $result_detail[0]["ad_description"];
            $detail_ad_description_detail    = $result_detail[0]["ad_description_detail"];
            $detail_price    = $result_detail[0]["price"];
            $detail_price_delivery    = $result_detail[0]["price_delivery"];

            $show_description = true;

            $list_images_thumb = [];

            foreach (glob("*upload/thumb/*") as $filename) {
                if(preg_match("#upload/thumb/thumb_".$get_id."-#", $filename)){
                    array_push($list_images_thumb, $filename);
                }
            }

            $list_images_large = [];

            foreach (glob("*upload/large/*") as $filename) {
                if(preg_match("#upload/large/".$get_id."-[0-9]+.jpg#", $filename)){
                    array_push($list_images_large, $filename);
                }
            }
        }

        break;

    case "add": // formulaire d'ajout d'un produit
        /*
            Le formulaire comporte les champs suivants:
                - un select: Catégorie > Sous-catégorie
                    => la liste d'options est basée sur deux listes de la DB:
                        - catégorie de niveau 1
                        - catégorie de niveau 2
                - un select: état de l'objet
                    => la liste d'options est basée sur la liste d'états possibles de la DB
                - un select: choix du designer
                    => la liste d'options est basée sur la liste de designers de la DB
                - un select: choix de la manufacture
                    => la liste d'options est basée sur la liste de manufactures de la DB:
                - un input text: nom de l'objet
                - un textArea: brève description
                - un textArea: description complète
                - un input number: prix htva
                - un input number: prix de livraison
        */

        $post_level_2           = isset($_POST["level_2"])      ? filter_input(INPUT_POST, 'level_2', FILTER_SANITIZE_SPECIAL_CHARS)                : null;
        $post_shape        = isset($_POST["shape"])   ? filter_input(INPUT_POST, 'shape', FILTER_SANITIZE_SPECIAL_CHARS)             : null;
        $post_designer   = isset($_POST["designer"])  ? filter_input(INPUT_POST, 'designer', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
        $post_manufacture   = isset($_POST["manufacture"])  ? filter_input(INPUT_POST, 'manufacture', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
        $post_ad_title   = isset($_POST["ad_title"])  ? filter_input(INPUT_POST, 'ad_title', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
        $post_ad_description   = isset($_POST["ad_description"])  ? filter_input(INPUT_POST, 'ad_description', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
        $post_ad_description_detail   = isset($_POST["ad_description_detail"])  ? filter_input(INPUT_POST, 'ad_description_detail', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
        $post_price_htva   = isset($_POST["price_htva"])  ? filter_input(INPUT_POST, 'price_htva', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
        $post_price_delivery   = isset($_POST["price_delivery"])  ? filter_input(INPUT_POST, 'price_delivery', FILTER_SANITIZE_SPECIAL_CHARS)    : null;

        /*  Récupération des différentes listes pour remplir les select
            Attention: les options du select "Catégorie associée" sont une combinaison: "nom_catégorie_level_1 > nom_catégorie_level_2", 
                        avec une valeur égale à l'id de la catégorie level 2
        */

        $liste_categories_level_2 = getCategoryLevel2();
        $liste_categories_mixed = [];

        foreach($liste_categories_level_2 as $one_cat_level_2){
            if($one_cat_level_2["cat_1_is_visible"] == 0 || $one_cat_level_2["cat_2_is_visible"] == 0){
                $liste_categories_mixed[$one_cat_level_2["category_level_2_id"]] = $one_cat_level_2["level_1"]." > ".$one_cat_level_2["level_2"].$text_missing_data;
            }else{
                $liste_categories_mixed[$one_cat_level_2["category_level_2_id"]] = $one_cat_level_2["level_1"]." > ".$one_cat_level_2["level_2"];
            }
        }

        // On va récupérer la liste des différents états
        // s'il y en a qui ont un statut "caché" ("is_visible" == 0)
        // on indique un message à côté

        $liste_etats_brut = getShape();
        $liste_etats = [];

        foreach($liste_etats_brut as $one_etat){
            if($one_etat["is_visible"] == 0){
                $liste_etats[$one_etat["shape_id"]] = $one_etat["shape_title"].$text_missing_data;
            }else{
                $liste_etats[$one_etat["shape_id"]] = $one_etat["shape_title"];
            }
        }

        // On va récupérer la liste des différents designers
        // s'il y en a qui ont un statut "caché" ("is_visible" == 0)
        // on indique un message à côté

        $liste_designers_brut = getDesigner();
        $list_designers = [];

        foreach($liste_designers_brut as $one_etat){
            if($one_etat["is_visible"] == 0){
                $list_designers[$one_etat["id"]] = $one_etat["full_name"].$text_missing_data;
            }else{
                $list_designers[$one_etat["id"]] = $one_etat["full_name"];
            }
        }

        // On va récupérer la liste des différentes manufactures
        // s'il y en a qui ont un statut "caché" ("is_visible" == 0)
        // on indique un message à côté

        $liste_manufactures_brut = getManufacturer();
        $liste_manufactures = [];

        foreach($liste_manufactures_brut as $one_manufacture){
            if($one_manufacture["is_visible"] == 0){
                $liste_manufactures[$one_manufacture["id"]] = $one_manufacture["manufacturer"].$text_missing_data;
            }else{
                $liste_manufactures[$one_manufacture["id"]] = $one_manufacture["manufacturer"];
            }
        }
        
        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Ajouter un produit</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Catégorie > Sous-catégorie", ["name" => "level_2", "value" => $post_level_2], $liste_categories_mixed,"=== choix ===", true);
        $input[] = addSelect("Etat de l'objet", ["name" => "shape", "value" => $post_shape], $liste_etats,"=== choix ===", true);
        $input[] = addSelect("Designer", ["name" => "designer", "value" => $post_designer], $list_designers,"=== choix ===", true);
        $input[] = addSelect("Manufacture", ["name" => "manufacture", "value" => $post_manufacture], $liste_manufactures,"=== choix ===", true);
        $input[] = addInput('Nom de l\'objet', ["type" => "text", "value" => $post_ad_title, "name" => "ad_title", "class" => "u-full-width"], true, "twelve columns");
        $input[] = addTextarea('Brève description', array("name" => "ad_description", "class" => "u-full-width"), $post_ad_description, true, "twelve columns");
        $input[] = addTextarea('Description complète', array("name" => "ad_description_detail", "class" => "u-full-width"), $post_ad_description_detail, true, "twelve columns");

        $input[] = addInput('Prix htva', ["type" => "number", "value" => $post_price_htva, "name" => "price_htva", "class" => "u-full-width"], true, "five columns");
        $input[] = addInput('Prix de la livraison', ["type" => "number", "value" => $post_price_delivery, "name" => "price_delivery", "class" => "u-full-width"], true, "five columns");


        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "envoyer", "name" => "submit"], "\t\t<br />\n");

        $show_form = form("form_contact", "index.php?p=".$url_page."&action=add", "post", $input);

        if($show_form != false){
            // définition de la variable view qui sera utilisée pour afficher la partie du <body> du html nécessaire à l'affichage du formulaire
            $page_view = "product_form";

            // si form() retourne false, l'insertion peut avoir lieu
        }else{
            // création d'un tableau qui contiendra les données à passer à la fonction d'insert
            // Attention: le prix TVA inclus et le montant de la TVA sont calculés ici
            //              l'id de l'administrateur sera récupéré dans la session

            $data_values = array();
            $data_values["category_level_2_id"]      = $post_level_2;
            $data_values["admin_id"]      = $_SESSION["admin_id"];
            $data_values["shape_id"]         = $post_shape;
            $data_values["designer_id"] = $post_designer;
            $data_values["manufacturer_id"] = $post_manufacture;
            $data_values["ad_title"] = $post_ad_title;
            $data_values["ad_description"] = $post_ad_description;
            $data_values["ad_description_detail"] = $post_ad_description_detail;
            $data_values["price"] = ($post_price_htva * $taux_tva) + $post_price_htva;
            $data_values["price_htva"] = $post_price_htva;
            $data_values["amount_tva"] = $post_price_htva * $taux_tva;
            $data_values["price_delivery"] = $post_price_delivery;

            // exécution de la requête
            if(insertProduct($data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>données insérées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>erreur lors de l'insertion des données</p>";
                $msg_class = "error";
            }

            $page_view = "product_liste";
            $result = getProduct(null, $get_alpha);
            $alphabet = range('A', 'Z');

        }
        break;

    case "update": // formulaire de mise à jour d'un produit
        /*
            Le formulaire comporte les champs suivants:
                - un select: Catégorie > Sous-catégorie
                    => la liste d'options est basée sur deux listes de la DB:
                        - catégorie de niveau 1
                        - catégorie de niveau 2
                - un select: état de l'objet
                    => la liste d'options est basée sur la liste d'états possibles de la DB
                - un select: choix du designer
                    => la liste d'options est basée sur la liste de designers de la DB
                - un select: choix de la manufacture
                    => la liste d'options est basée sur la liste de manufactures de la DB:
                - un input text: nom de l'objet
                - un textArea: brève description
                - un textArea: description complète
                - un input number: prix htva
                - un input number: prix de livraison
        */

        if(empty($_POST)){ // Il faut prévoir le cas où certaines données déjà présentes dans la DB sont absentes 
                            // => condition sur chaque case du tableau, si elle n'est pas là, elle est considérée comme vide 
            $result = getProduct($get_id);

            $post_level_2      = isset($result[0]["category_level_2_id"]) ? $result[0]["category_level_2_id"] : null;
            $post_shape      = isset($result[0]["shape_id"]) ? $result[0]["shape_id"] : null;
            $post_designer      = isset($result[0]["designer_id"]) ? $result[0]["designer_id"] : null;
            $post_manufacture      = isset($result[0]["manufacturer_id"]) ? $result[0]["manufacturer_id"] : null;
            $post_ad_title      = isset($result[0]["ad_title"]) ? $result[0]["ad_title"] : null;
            $post_ad_description      = isset($result[0]["ad_description"]) ? $result[0]["ad_description"] : null;
            $post_ad_description_detail      = isset($result[0]["ad_description_detail"]) ? $result[0]["ad_description_detail"] : null;
            $post_price_htva      = isset($result[0]["price_htva"]) ? $result[0]["price_htva"] : null;
            $post_price_delivery      = isset($result[0]["price_delivery"]) ? $result[0]["price_delivery"] : null;
        }else{
            $post_level_2           = isset($_POST["level_2"])      ? filter_input(INPUT_POST, 'level_2', FILTER_SANITIZE_SPECIAL_CHARS)                : null;
            $post_shape        = isset($_POST["shape_id"])   ? filter_input(INPUT_POST, 'shape_id', FILTER_SANITIZE_SPECIAL_CHARS)             : null;
            $post_designer   = isset($_POST["designer"])  ? filter_input(INPUT_POST, 'designer', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
            $post_manufacture   = isset($_POST["manufacture"])  ? filter_input(INPUT_POST, 'manufacture', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
            $post_ad_title   = isset($_POST["ad_title"])  ? filter_input(INPUT_POST, 'ad_title', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
            $post_ad_description   = isset($_POST["ad_description"])  ? filter_input(INPUT_POST, 'ad_description', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
            $post_ad_description_detail   = isset($_POST["ad_description_detail"])  ? filter_input(INPUT_POST, 'ad_description_detail', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
            $post_price_htva   = isset($_POST["price_htva"])  ? filter_input(INPUT_POST, 'price_htva', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
            $post_price_delivery   = isset($_POST["price_delivery"])  ? filter_input(INPUT_POST, 'price_delivery', FILTER_SANITIZE_SPECIAL_CHARS)    : null;
        }

        /*  Récupération des différentes listes pour remplir les select
            Attention: les options du select "Catégorie associée" sont une combinaison: "nom_catégorie_level_1 > nom_catégorie_level_2", 
                        avec une valeur égale à l'id de la catégorie level 2
        */

        $liste_categories_level_2 = getCategoryLevel2();
        $liste_categories_mixed = [];

        foreach($liste_categories_level_2 as $one_cat_level_2){
            $liste_categories_mixed[$one_cat_level_2["category_level_2_id"]] = $one_cat_level_2["level_1"]." > ".$one_cat_level_2["level_2"];
        }

        // On va récupérer la liste des différents états
        // s'il y en a qui ont un statut "caché" ("is_visible" == 0)
        // on indique un message à côté

        $liste_etats_brut = getShape();
        $liste_etats = [];

        foreach($liste_etats_brut as $one_etat){
            if($one_etat["is_visible"] == 0){
                $liste_etats[$one_etat["shape_id"]] = $one_etat["shape_title"].$text_missing_data;
            }else{
                $liste_etats[$one_etat["shape_id"]] = $one_etat["shape_title"];
            }
        }

        // On va récupérer la liste des différents designers
        // s'il y en a qui ont un statut "caché" ("is_visible" == 0)
        // on indique un message à côté

        $liste_designers_brut = getDesigner();
        $list_designers = [];

        foreach($liste_designers_brut as $one_etat){
            if($one_etat["is_visible"] == 0){
                $list_designers[$one_etat["id"]] = $one_etat["full_name"].$text_missing_data;
            }else{
                $list_designers[$one_etat["id"]] = $one_etat["full_name"];
            }
        }

        // On va récupérer la liste des différentes manufactures
        // s'il y en a qui ont un statut "caché" ("is_visible" == 0)
        // on indique un message à côté

        $liste_manufactures_brut = getManufacturer();
        $liste_manufactures = [];

        foreach($liste_manufactures_brut as $one_manufacture){
            if($one_manufacture["is_visible"] == 0){
                $liste_manufactures[$one_manufacture["id"]] = $one_manufacture["manufacturer"].$text_missing_data;
            }else{
                $liste_manufactures[$one_manufacture["id"]] = $one_manufacture["manufacturer"];
            }
        }
        
        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Mettre à jour un produit</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Catégorie > Sous-catégorie", ["name" => "level_2", "value" => $post_level_2], $liste_categories_mixed,"=== choix ===", true);
        $input[] = addSelect("Etat de l'objet", ["name" => "shape_id", "value" => $post_shape], $liste_etats,"=== choix ===", true);
        $input[] = addSelect("Designer", ["name" => "designer", "value" => $post_designer], $list_designers,"=== choix ===", true);
        $input[] = addSelect("Manufacture", ["name" => "manufacture", "value" => $post_manufacture], $liste_manufactures,"=== choix ===", true);
        $input[] = addInput('Nom de l\'objet', ["type" => "text", "value" => $post_ad_title, "name" => "ad_title", "class" => "u-full-width"], true, "twelve columns");
        $input[] = addTextarea('Brève description', array("name" => "ad_description", "class" => "u-full-width"), $post_ad_description, true, "twelve columns");
        $input[] = addTextarea('Description complète', array("name" => "ad_description_detail", "class" => "u-full-width"), $post_ad_description_detail, true, "twelve columns");

        $input[] = addInput('Prix htva', ["type" => "number", "value" => $post_price_htva, "name" => "price_htva", "class" => "u-full-width"], true, "five columns");
        $input[] = addInput('Prix de la livraison', ["type" => "number", "value" => $post_price_delivery, "name" => "price_delivery", "class" => "u-full-width"], true, "five columns");


        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "envoyer", "name" => "submit"], "\t\t<br />\n");

        $show_form = form("form_contact", "index.php?p=".$url_page."&action=update&id=".$get_id, "post", $input);

        if($show_form != false){
            // définition de la variable view qui sera utilisée pour afficher la partie du <body> du html nécessaire à l'affichage du formulaire
            $page_view = "designer_form";
        }else{

            // création d'un tableau qui contiendra les données à passer à la fonction d'insert
            // Attention: le prix TVA inclus et le montant de la TVA sont calculés ici
            //              l'id de l'administrateur sera récupéré dans la session
            //                  => l'id admin final sera celui de l'admin qui a fait la dernière modification

            $data_values = array();
            $data_values["category_level_2_id"]      = $post_level_2;
            $data_values["admin_id"]      = $_SESSION["admin_id"];
            $data_values["shape_id"]         = $post_shape;
            $data_values["designer_id"] = $post_designer;
            $data_values["manufacturer_id"] = $post_manufacture;
            $data_values["ad_title"] = $post_ad_title;
            $data_values["ad_description"] = $post_ad_description;
            $data_values["ad_description_detail"] = $post_ad_description_detail;
            $data_values["price"] = ($post_price_htva * $taux_tva) + $post_price_htva;
            $data_values["price_htva"] = $post_price_htva;
            $data_values["amount_tva"] = $post_price_htva * $taux_tva;
            $data_values["price_delivery"] = $post_price_delivery;

            // exécution de la requête
            if(updateProduct($get_id, $data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>données modifiées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>erreur lors de la modification des données</p>";
                $msg_class = "error";
            }

            $page_view = "product_liste";
            $result = getProduct(null, $get_alpha);
            $alphabet = range('A', 'Z');
        }

        break;

    case "showHide": // mise à jour du statut "visible" d'un produit
        $get_product_id = isset($_GET["id"]) ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) : null;

        if(showHideProduct($get_product_id)){
            // message de succes qui sera affiché dans le <body>
            $msg = "<p>mise à jour de l'état réalisée avec succès</p>";
            $msg_class = "success";
        }else{
            // message d'erreur qui sera affiché dans le <body>
            $msg = "<p>erreur lors de la mise à jour de l'état</p>";
            $msg_class = "error";
        }

        $page_view = "product_liste";
        $alphabet = range('A', 'Z');
        $result = getProduct(null, $get_alpha);

        if(!is_null($get_id) && is_numeric($get_id)){
            $result_detail = getProduct($get_id);

            $detail_ad_title        = $result_detail[0]["ad_title"];
            $detail_ad_description            = $result_detail[0]["ad_description"];
            $detail_ad_description_detail    = $result_detail[0]["ad_description_detail"];
            $detail_price    = $result_detail[0]["price"];
            $detail_price_delivery    = $result_detail[0]["price_delivery"];

            $show_description = true;

            $list_images_thumb = [];

            foreach (glob("*upload/thumb/*") as $filename) {
                if(preg_match("#upload/thumb/thumb_".$get_id."-#", $filename)){
                    array_push($list_images_thumb, $filename);
                }
            }

            $list_images_large = [];

            foreach (glob("*upload/large/*") as $filename) {
                if(preg_match("#upload/large/".$get_id."-#", $filename)){
                    array_push($list_images_large, $filename);
                }
            }

        }

        break;
    case "addphoto":
        $page_view = "product_liste";
        $alphabet = range('A', 'Z');
        $result = getProduct(null, $get_alpha);

        // Lorsqu'on ajoute une photo, on regarde les images qui sont disponibles
        // et on les affiche

        $list_images_thumb = [];

        foreach (glob("*upload/thumb/*") as $filename) {
            if(preg_match("#upload/thumb/thumb_".$get_id."-[0-9]+.jpg#", $filename)){
                $limit_down_if = strpos($filename, '-');
                $limit_up_if = strpos($filename, '.');
                $totake_if = $limit_up_if - $limit_down_if;
                $img_id = (int)substr($filename, $limit_down_if+1, $totake_if-1);
                $list_images_thumb[$img_id] = $filename;
            }
        }

        $list_images_large = [];

        foreach (glob("*upload/large/*") as $filename) {
            if(preg_match("#upload/large/".$get_id."-[0-9]+.jpg#", $filename)){
                $limit_down_if = strpos($filename, '-');
                $limit_up_if = strpos($filename, '.');
                $totake_if = $limit_up_if - $limit_down_if;
                $img_id = (int)substr($filename, $limit_down_if+1, $totake_if-1);
                $list_images_large[$img_id] = $filename;
            }
        }

        if(!is_null($get_id) && is_numeric($get_id)){
            $result_detail = getProduct($get_id);

            $detail_ad_title        = $result_detail[0]["ad_title"];
            $detail_ad_description            = $result_detail[0]["ad_description"];
            $detail_ad_description_detail    = $result_detail[0]["ad_description_detail"];
            $detail_price    = $result_detail[0]["price"];
            $detail_price_delivery    = $result_detail[0]["price_delivery"];

            $show_description = true;

        }
        
        // On vérifie qu'il y a bien une image qui a été chargée.
        // S'il n'y a pas d'images, on réaffiche simplement la page

        if(isset($_GET["phototype"]) && file_exists($_FILES["fileToUpload"]["tmp_name"])){

            if($_GET["phototype"] === "thumb"){

                // Détermination de l'index de la possible future nouvelle image miniature: 
                // s'il y a d'autres images, l'index sera l'index de celle qui est la plus grande + 1
                // sinon, l'index est égal à 1
                if(count($list_images_thumb)>0){
                    $length_list_images = count($list_images_thumb);
                    $max_key = 0;
                    foreach($list_images_thumb as $number => $x_value){
                        if($max_key<$number){
                            $max_key = $number;
                        }
                    }
                    $new_img_id = (int)$max_key + 1;
                }
                else{
                    $new_img_id = 1;
                }

                $target_dir = "./upload/thumb/";
                $target_file = $target_dir."thumb_".$get_id. "-".$new_img_id.".jpg";
            }
            else if(isset($_GET["phototype"]) && $_GET["phototype"] === "large"){

                // Détermination de l'index de la possible future nouvelle image large: 
                // s'il y a d'autres images, l'index sera l'index de celle qui est la plus grande + 1
                // sinon, l'index est égal à 1
                if(count($list_images_large)>0){
                    $length_list_images = count($list_images_large);
                    $max_key = 0;
                    foreach($list_images_large as $number => $x_value){
                        if($max_key<$number){
                            $max_key = $number;
                        }
                    }
                    $new_img_id = (int)$max_key + 1;
                }
                else{
                    $new_img_id = 1;
                }

                $target_dir = "./upload/large/";
                $target_file = $target_dir.$get_id. "-".$new_img_id.".jpg";
            }
            // Vérifier l'image et la mettre dans le bon dossier
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            // Vérifier que c'est une image
            if(isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if($check !== false) {

                    // Vérifie la taille de l'image
                    if ($_FILES["fileToUpload"]["size"] > 500000) {
                        $msg = "<p>Désolé, l'image est trop lourde.</p>";
                        $msg_class = "error";
                        $uploadOk = 0;
                    } else {
                        // Vérifie le type de l'image, seuls jpg, png, jpeg et gif sont acceptés
                        if(strtolower($imageFileType) != "jpg") {
                            $msg = "<p>Seul le type JPG est permis.</p>";
                            $msg_class = "error";
                            $uploadOk = 0;
                        } else{
                            // Vérifie si $uploadOk est à 0 en cas d'erreur
                            if ($uploadOk == 0) {
                                $msg = "<p>Désolé, l'image n'a pas été ajoutée.</p>";
                                $msg_class = "error";
                            // Essai d'ajout de l'image
                            } else {
                                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                                    $msg = "<p>Le fichier a bien été ajouté.</p>";
                                    $msg_class = "success";
                                } else {
                                    $msg = "<p>Une erreur s'est produite.</p>";
                                    $msg_class = "error";
                                }
                            }
                        }
                    }
                } else {
                    echo "<center>Le fichier n'est pas une image.</center>";
                    $uploadOk = 0;
                }
            }
        }

        // Après avoir ajouté (ou non) l'image, on regarde à nouveau s'il y a des images

        $list_images_thumb = [];

        foreach (glob("*upload/thumb/*") as $filename) {
            if(preg_match("#upload/thumb/thumb_".$get_id."-#", $filename)){
                array_push($list_images_thumb, $filename);
            }
        }

        $list_images_large = [];

        foreach (glob("*upload/large/*") as $filename) {
            if(preg_match("#upload/large/".$get_id."-[0-9]+.jpg#", $filename)){
                array_push($list_images_large, $filename);
            }
        }

        break;
}

?>