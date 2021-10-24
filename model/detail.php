<!-- 
    Page de détails d'un produit, données récupérées sur base d'un ID
-->

<?php
    $page_view = "detail";
    include_once("lib/public_product.php");

    $product_id = isset($_GET["id"]) ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT) : null;

    $product_details_gross = getProductById($product_id);


    // On a demandé à la DB de renvoyer les données d'un produit 
    // MAIS uniquement s'il peut être vu
    // Donc on vérifie ici que le résultat SQL n'est pas false
    if($product_details_gross){
        $product_details = $product_details_gross[0];

        $list_images_thumb = [];
    
        // On va récupérer dans le dossier upload les photos
        // en se basant sur l'id du produit
        foreach (glob("*upload/thumb/*") as $filename) {
            if(preg_match("#upload/thumb/thumb_".$product_id."-#", $filename)){
                array_push($list_images_thumb, $filename);
            }
        }    
    } else{
        // Si le produit ne peut pas être vu car la requête SQL renvoie "false" 
        // (par exemple, si le produit est caché ou n'existe pas dans la DB),
        // le client voit un message et un lien de retour vers la page index
 
        $msg_redirect = "Ce produit n'est pas disponible <br> <a href='index.php'>Retourner à l'accueil</a>";
    }

    // Pour une question de clarté on va mettre les données du tableau dans des variables 
    // en prenant en compte des cas où les données sont vides (par ex. pour les nom et prénom du designer)
    $product_title = !empty($product_details["ad_title"])? $product_details["ad_title"]: "Titre manquant";
    $product_description = !empty($product_details["ad_description"])? $product_details["ad_description"]: "Description manquante";
    $product_description_detail = !empty($product_details["ad_description_detail"])? $product_details["ad_description_detail"]: "Description manquante";
    $product_price = !empty($product_details["price"])? $product_details["price"]: "Contactez-nous pour le prix";
    $product_price_delivery = !empty($product_details["price_delivery"])? $product_details["price_delivery"]: " - ";
    
    // Si un produit dans la DB n'a pas de données disant s'il peut être montré ou non,
    // il est considéré comme devant être caché par défaut
    $product_visible = !empty($product_details["is_visible"])? $product_details["is_visible"]: 0;
    $product_level_1 = !empty($product_details["level_1"])? $product_details["level_1"]: "Non défini";
    $product_level_2 = !empty($product_details["level_2"])? $product_details["level_2"]: "Non défini";
    $product_firstname = !empty($product_details["firstname"])? $product_details["firstname"]: " - ";
    $product_lastname = !empty($product_details["lastname"])? $product_details["lastname"]: " - ";
    $product_manufacturer = !empty($product_details["manufacturer"])? $product_details["manufacturer"]: "-";
?>