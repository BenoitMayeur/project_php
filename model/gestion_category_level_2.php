<?php
adminProtection();
include_once("lib/category_level_1.php");
include_once("lib/category_level_2.php");

$url_page = "gestion_category_level_2";
$get_action     = isset($_GET["action"]) ? $_GET["action"] : "list";
$get_category_level_2_id   = isset($_GET["category_level_2_id"]) ? filter_input(INPUT_GET, 'category_level_2_id', FILTER_SANITIZE_NUMBER_INT) : null;

/*
    En fonction du paramètre $get_action, on affichera:
        - la liste des catégories de niveau 2
        - le formulaire d'ajout d'une catégorie de niveau 2
        - le formulaire de mise à jour d'une catégorie de niveau 2
        Ou on mettra à jour le statut "visible" d'une catégorie de niveau 2
*/

switch($get_action){
    case "list": // liste des catégories de niveau 2
        $result = getCategoryLevel2();
        $list_cat_level_1 = getCategoryLevel1();

        $page_view = "category_level_2_liste";
        break;

    case "add": // formulaire d'ajout d'une catégorie de niveau 2
        // la page d'ajout d'une catégorie montre un select avec une liste d'options de catégorie de niveau 1
        // et un input pour donner un nom à cette nouvelle catégorie de niveau 2
        
        $post_level_2        = isset($_POST["level_2"])       ? filter_input(INPUT_POST, 'level_2', FILTER_SANITIZE_SPECIAL_CHARS)         : null;
        $post_level_1        = isset($_POST["level_1"])       ? filter_input(INPUT_POST, 'level_1', FILTER_SANITIZE_SPECIAL_CHARS)         : null;

        // Pour remplir le select d'une catégorie de niveau 1:
        // on récupère la liste de catégories de niveau 1, on la modifie quand on la met dans une array $list_options 
        // $list_options est un tableau associatif sur le modèle "id_level => nom_level"

        $list_category_level_1 = getCategoryLevel1();
        $list_options = [];

        // Quand on remplit le tableau de catégories niveau 1, on indique un texte à côté de celles qui sont "cachées"
        foreach($list_category_level_1 as $cat_level_1) {
            if($cat_level_1["is_visible"] == 0){
                $list_options[$cat_level_1["category_level_1_id"]] = $cat_level_1["level_1"].$text_missing_data;;
            } else{
                $list_options[$cat_level_1["category_level_1_id"]] = $cat_level_1["level_1"];
            }
        }

        $input = [];

        $input[] = addLayout("<h4>Ajouter une sous-catégorie</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Catégorie associée", ["name" => "level_1", "value" => $post_level_1], $list_options,"=== choix ===", true);
        $input[] = addInput("Nom de la sous-catégorie", ["type" => "text", "value" => $post_level_2, "name" => "level_2", "class" => "u-full-width"], true, "twelve columns");
        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "envoyer", "name" => "submit"], "\t\t<br />\n");

        $show_form = form("form_contact", "index.php?p=".$url_page."&action=add", "post", $input);

        if($show_form != false){
            $page_view = "category_level_2_form";
        }else{

            $data_values = array();
            $data_values["level_1"]          = $post_level_1;
            $data_values["level_2"]          = $post_level_2;

            // exécution de la requête
            if(insertCategoryLevel2($data_values)){
                $msg = "<p>données insérées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>erreur lors de l'insertion des données</p>";
                $msg_class = "error";
            }

            $result = getCategoryLevel2();
            $list_cat_level_1 = getCategoryLevel1();

            $page_view = "category_level_2_liste";
        }
        break;

    case "update": // formulaire de mise à jour d'une catégorie de niveau 2
        // la page de mise à jour d'une catégorie montre un select avec une liste d'options de catégorie de niveau 1
        // et un input pour donner un autre nom à cette catégorie de niveau 2

        if(empty($_POST)){
            $result = getCategoryLevel2($get_category_level_2_id);

            $post_level_2        = $result[0]["level_2"];
            $post_level_1        = $result[0]["category_level_1_id"];

        }else{
            // récupération / initialisation des données qui transitent via le formulaire
            $post_level_2        = isset($_POST["level_2"])       ? filter_input(INPUT_POST, 'level_2', FILTER_SANITIZE_SPECIAL_CHARS)         : null;
            $post_level_1        = isset($_POST["level_1"])       ? filter_input(INPUT_POST, 'level_1', FILTER_SANITIZE_SPECIAL_CHARS)         : null;

        }
        
        // Pour remplir le select d'une catégorie de niveau 1:
        // on récupère la liste de catégories de niveau 1, on la modifie quand on la met dans une array $list_options 
        // $list_options est un tableau associatif sur le modèle "id_level => nom_level"

        $list_category_level_1 = getCategoryLevel1();
        $list_options = [];

        // Quand on remplit le tableau de catégories niveau 1, on indique un texte à côté de celles qui sont "cachées"
        foreach($list_category_level_1 as $cat_level_1) {
            if($cat_level_1["is_visible"] == 0){
                $list_options[$cat_level_1["category_level_1_id"]] = $cat_level_1["level_1"].$text_missing_data;;
            } else{
                $list_options[$cat_level_1["category_level_1_id"]] = $cat_level_1["level_1"];
            }
        }

        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Modifier une sous-catégorie</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Catégorie associée", ["name" => "level_1", "value" => $post_level_1], $list_options, $post_level_1, true);
        $input[] = addInput("Nom de la sous-catégorie", ["type" => "text", "value" => $post_level_2, "name" => "level_2", "class" => "u-full-width"], true, "twelve columns");
        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "envoyer", "name" => "submit"], "\t\t<br />\n");

        $show_form = form("form_contact", "index.php?p=".$url_page."&action=update&category_level_2_id=".$get_category_level_2_id, "post", $input);

        if($show_form != false){

            $page_view = "category_level_2_form";

            // si form() retourne false, l'insertion peut avoir lieu
        }else{
            $data_values = array();
            $data_values["level_2"]          = $post_level_2;
            $data_values["category_level_1_id"]          = $post_level_1;

            // exécution de la requête
            if(updateCategoryLevel2($get_category_level_2_id, $data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>données modifiées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>erreur lors de la modification des données</p>";
                $msg_class = "error";
            }

            // récupération des catégories correspondantes
            $result = getCategoryLevel2();
            $list_cat_level_1 = getCategoryLevel1();

            $page_view = "category_level_2_liste";
        }
        break;

    case "showHide": // mise à jour du statut "visible" d'une catégorie de niveau 2
        if(showHideCategoryLevel2($get_category_level_2_id)){
            // message de succes qui sera affiché dans le <body>
            $msg = "<p>mise à jour de l'état réalisée avec succès</p>";
            $msg_class = "success";
        }else{
            // message d'erreur qui sera affiché dans le <body>
            $msg = "<p>erreur lors de la mise à jour de l'état</p>";
            $msg_class = "error";
        }

        // récupération des catégories correspondantes
        $result = getCategoryLevel2();

        $list_cat_level_1 = getCategoryLevel1();

        $page_view = "category_level_2_liste";

        break;
}

?>