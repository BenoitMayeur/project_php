<?php
adminProtection();
include_once("lib/category_level_1.php");

$url_page = "gestion_category_level_1";
$get_action = isset($_GET["action"]) ? $_GET["action"] : "list";
$get_category_level_1_id   = isset($_GET["category_level_1_id"]) ? filter_input(INPUT_GET, 'category_level_1_id', FILTER_SANITIZE_NUMBER_INT) : null;

/*
    En fonction du paramètre $get_action, on affichera:
        - la liste des catégories de niveau 1
        - le formulaire d'ajout d'une catégorie de niveau 1
        - le formulaire de mise à jour d'une catégorie de niveau 1
        Ou on mettra à jour le statut "visible" d'une catégorie de niveau 1
*/

switch($get_action){
    case "list": // liste des catégories de niveau 1
        $result = getCategoryLevel1();

        $page_view = "category_level_1_liste";
        break;

    case "add": // formulaire d'ajout d'une catégorie de niveau 1
        // la page d'ajout d'une catégorie montre un input pour donner un nom à cette nouvelle catégorie
        $post_level_1 = isset($_POST["level_1"]) ? filter_input(INPUT_POST, 'level_1', FILTER_SANITIZE_SPECIAL_CHARS) : null;

        $input = [];
        $input[] = addLayout("<h4>Ajouter une catégorie</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput("Nom de la catégorie", ["type" => "text", "value" => $post_level_1, "name" => "level_1", "class" => "u-full-width"], true, "twelve columns");
        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "envoyer", "name" => "submit"], "\t\t<br />\n");

        $show_form = form("form_contact", "index.php?p=".$url_page."&action=add", "post", $input);

        if($show_form != false){
            $page_view = "category_level_1_form";
        }else{
            $data_values = array();
            $data_values["level_1"] = $post_level_1;

            // exécution de la requête
            if(insertCategoryLevel1($data_values)){
                $msg = "<p>données insérées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>erreur lors de l'insertion des données</p>";
                $msg_class = "error";
            }

            $result = getCategoryLevel1();

            $page_view = "category_level_1_liste";
        }
        break;

    case "update": // formulaire de mise à jour d'une catégorie de niveau 1
        // la page de mise à jour d'une catégorie montre un input pour changer le nom de cette catégorie
        if(empty($_POST)){
            $result = getCategoryLevel1($get_category_level_1_id);
            $post_level_1 = $result[0]["level_1"];
        }else{
            // récupération / initialisation des données qui transitent via le formulaire
            $post_level_1 = isset($_POST["level_1"]) ? filter_input(INPUT_POST, 'level_1', FILTER_SANITIZE_SPECIAL_CHARS) : null;
        }

        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Modifier une catégorie</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput("Nom de la catégorie", ["type" => "text", "value" => $post_level_1, "name" => "level_1", "class" => "u-full-width"], true, "twelve columns");
        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "envoyer", "name" => "submit"], "\t\t<br />\n");

        $show_form = form("form_contact", "index.php?p=".$url_page."&action=update&category_level_1_id=".$get_category_level_1_id, "post", $input);

        if($show_form != false){
            $page_view = "category_level_1_form";

            // si form() retourne false, l'insertion peut avoir lieu
        }else{
            $data_values = array();
            $data_values["level_1"] = $post_level_1;

            // exécution de la requête
            if(updateCategoryLevel1($get_category_level_1_id, $data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>données modifiées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>erreur lors de la modification des données</p>";
                $msg_class = "error";
            }

            // récupération des catégories correspondantes
            $result = getCategoryLevel1();

            $page_view = "category_level_1_liste";
        }
        break;

    case "showHide": // mise à jour du statut "visible" d'une catégorie de niveau 1
        if(showHideCategoryLevel1($get_category_level_1_id)){
            // message de succes qui sera affiché dans le <body>
            $msg = "<p>mise à jour de l'état réalisée avec succès</p>";
            $msg_class = "success";
        }else{
            // message d'erreur qui sera affiché dans le <body>
            $msg = "<p>erreur lors de la mise à jour de l'état</p>";
            $msg_class = "error";
        }

        // récupération des catégories correspondantes
        $result = getCategoryLevel1();

        $page_view = "category_level_1_liste";

        break;
}

?>