<?php
    $page_view = "home";
    include_once("lib/public_product.php");


    $get_page = isset($_GET["page"])? $_GET["page"] : "1";
    $get_id = isset($_GET["id"])? $_GET["id"] : "";

    // Montrer les produits sur la page
    /////////////////////////////////////////////////////////////////////////////////////
    
    $amount_per_page = 30;

    if ($get_page === "1") {
        $lower_lim = "0";
    }
    else {
        $lower_lim = ($get_page-1)*$amount_per_page;
    }
    $list_articles_public = getProductsPerPage($amount_per_page,$lower_lim);
    $msg_not_product = "Il n'y a pas de produit de ce type.";

    // Pagination
    /////////////////////////////////////////////////////////////////////////////////////
    $amount_products = getAmountProducts();
    $amount_pages_gross = ceil($amount_products/$amount_per_page);

    // Pour les liens |< et < de la pagination
    if($get_page == 1){
        $lower_text_pagination = "";
    } else{
        $local_lower_limit = $get_page == 1 ? 1 : $get_page-1;
        $lower_text_pagination = "<a class='link_pagination' href='index.php?page=1'>|<</a> <a class='link_pagination' href='index.php?page=".$local_lower_limit."'><</a>";
    }

    // Pour les liens > et >| de la pagination
    if($get_page == $amount_pages_gross || $amount_products == 0){
        $higher_text_pagination = "";
    } else{
        $local_higher_limit = $get_page+1;
        $higher_text_pagination = "<a class='link_pagination' href='index.php?page=".$local_higher_limit."'>></a> <a class='link_pagination' href='index.php?page=".$amount_pages_gross."'>>|</a>";
    }

    // Pour les nombres en eux-mêmes, avec leur lien vers chaque page 
    if($amount_pages_gross<=11){
        $lower_limit_page = 1;
        $higher_limit_page = $amount_pages_gross;
        $lower_dots_pagination = "";
        $higher_dots_pagination = "";
    } else{
        $lower_dots_pagination = $get_page-3 <= 1 ? "" : "...";
        $higher_dots_pagination = $get_page+10 >= $amount_pages_gross ? "" : "...";
        $lower_limit_page = $get_page-3 <= 0 ? 1 : $get_page-3;
        $higher_limit_page = $get_page+10 <= $amount_pages_gross ? $get_page+10: $amount_pages_gross;
    }
?>

