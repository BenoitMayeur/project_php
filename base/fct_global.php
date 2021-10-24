<?php

// Données globales
$text_missing_data = " (caché)";

// affichage de debugage -> print_r mis en forme

function print_q($val){
    echo "<pre style='background-color:#000;color:#3FBBD5;font-size:11px;z-index:99999;position:relative;'>";
    print_r($val);
    echo "</pre>";
}

function cutLengthText($text, $amount){

    return substr($text, 0, $amount+1);
}

function adminProtection(){
    if((!isset($_SESSION["admin_id"])) || (empty($_SESSION["admin_id"]))|| (!is_numeric($_SESSION["admin_id"]))){
        header("Location: index.php?p=login");
        exit;
    }
}

function adminMenu($public_menu = ""){
    ?>
    <div class="row">
        <nav class="nav-show">
            <div class="container">
                <ul>
                    <?php
                    if(isset($_SESSION["admin_id"])){
                    ?>
                    <li><a href="index.php?p=gestion_item">Gestion des pages</a></li>
                    <li><a href="index.php?p=gestion_admin">Gestion des utilisateurs</a></li>
                    <li>
                        <a>Gestion du shop</a>
                        <ul>
                            <li><a href="index.php?p=gestion_designer">Designer</a></li>
                            <li><a href="index.php?p=gestion_manufacturer">Manufacturier</a></li>
                            <li><a href="index.php?p=gestion_shape">Etat</a></li>
                            <li><a href="index.php?p=gestion_category_level_1">Catégories</a></li>
                            <li><a href="index.php?p=gestion_category_level_2">Sous-Catégories</a></li>
                            <li><a href="index.php?p=gestion_product">Produits</a></li>
                        </ul>
                    </li>
                    <li>
                        <a>Mon compte</a>
                        <ul>
                            <li><a href="#">Modifier mon mot-de-passe</a></li>
                            <li><a href="index.php?p=gestion_unlog">Déconnexion</a></li>
                        </ul>
                    </li>
                    <?php
                    }else {
                        if (is_array($public_menu)) {
                            foreach ($public_menu as $pm) {
                                $rm_id = $pm["item_id"];
                                $rm_menu = $pm["item_menu"];
                                echo "<li><a href=\"index.php?p=public_item&item_id=" . $rm_id . "\">" . $rm_menu . "</a></li>";
                            }
                        }
                    }
                    ?>
                    <li class="u-pull-right"><a href="index.php?p=login">Login</a></li>
                </ul>
            </div>
        </nav>
    </div>
    <?php
}

?>