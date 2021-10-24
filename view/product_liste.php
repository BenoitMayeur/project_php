<div class="row">
    <div class="six columns">
        <?php
        echo "<h5>Recherche alphabétique :</h5>";
        echo "<p>";
        foreach($alphabet as $lettre){
            echo "<a href='index.php?p=".$url_page."&alpha=".$lettre."' class='bt-action'>".$lettre."</a> ";
        }
        echo "</p>";
        ?>
    </div>
    <div class="six columns">
        <form action="index.php?p=<?php echo $url_page; ?>" method="get" id="search">

            <div>
                <input type="hidden" name="p" value="<?php echo $url_page; ?>" />
                <input type="text" id="quicherchez_vous" name="alpha" value="" placeholder="Tapez votre recherche ici" />
                <input type="submit" value="trouver" />
                <a href="index.php?p=<?php echo $url_page; ?>&action=add" class="button"><i class="fas fa-user-plus"></i> Ajouter</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="four columns">
        <?php
        if(is_array($result)){
            foreach($result as $r){
                $id         = $r["ad_id"];
                $title      = $r["ad_title"];
                $is_visible = $r["is_visible"];
                $level_1 = $r["level_1"];
                $level_2 = $r["level_2"];
                $firstname = $r["firstname"];
                $lastname = $r["lastname"];
                $manufacturer = $r["manufacturer"];
            
                // nb : peut également se faire dans la requête sql pour une raison d'optimisation avec un CASE THEN
                if($is_visible == 1){
                    $txt_nom = "<span class='title_product'>".$title."</span>";
                    $txt_visible = "<i class=\"fas fa-eye-slash\"></i>";
                    $txt_title = "Masquer cette entrée";
                }else{
                    $txt_nom = "<span style='color:#b1b1b1;'>" .$title."</span>";
                    $txt_visible = "<i class=\"fas fa-eye\"></i>";
                    $txt_title = "Réactiver cette entrée";
                }

                if($firstname || $lastname){
                    $name_designer = " ".$firstname. " ".$lastname;
                } else{
                    $name_designer = " / ";
                }

                if($manufacturer ){
                    $name_manufacturer = $manufacturer;
                } else{
                    $name_manufacturer = " / ";
                }

                echo "<div class='div_product_summary'>
                        <p>
                            <a href='index.php?p=".$url_page."&id=".$id."&alpha=".$get_alpha."' title='Voir les informations' class='bt-action'>
                                <i class=\"fas fa-info-circle\"></i>
                            </a> 
                            <a href='index.php?p=".$url_page."&action=update&alpha=".$get_alpha."&id=".$id."' title='Editer cette entrée' class='bt-action'>
                                <i class=\"far fa-edit\">
                                </i>
                            </a> 
                            <a href='index.php?p=".$url_page."&action=showHide&alpha=".$get_alpha."&id=".$id."' title='".$txt_title."' class='bt-action'>"
                                .$txt_visible."
                            </a> 
                        </p>
                        ".$txt_nom."
                        <div></div>
                        <p>
                            Catégorie:".$level_1." > ".$level_2."
                        </p>
                        <div></div>
                        <p>
                            Designer:".$name_designer."
                        </p>
                        <div></div>
                        <p>
                            Manufacturer:".$name_manufacturer."
                        </p>
                    </div>";
            }
        }else{
            echo "<p>Aucun résultat pour la lettre ".$get_alpha."</p>";
        }
        ?>
    </div>
    <div class="eight columns">
        <?php

        echo isset($msg) && !empty($msg) ? "<div class='missingfield $msg_class'>".$msg."</div>" : "";

        if(isset($show_description) && $show_description){
            ?>
            <h4><?php echo $detail_ad_title; ?></h4>
            <?php
                echo "<p>".$detail_ad_description."</p>";
                echo "<p>".$detail_ad_description_detail."</p>";
                echo "<p>Prix (TVA Inc.): ".$detail_price." euros</p>";
                echo "<p>Livraison: ".$detail_price_delivery." euros</p>";
                echo "<h5>Miniature(s)</h5>";
                if(count($list_images_thumb) > 0){
                    echo "<div class='div_list_images'>";
                    foreach($list_images_thumb as $link_image){
                        echo "<img src=".$link_image.">";
                    }
                    echo "</div>";
                }else{
                    echo "<p>Pas d'images</p>";
                }
            ?>
            <div class="div_add_image">
                <form action="index.php?p=<?php echo $url_page; ?>&action=addphoto&alpha=<?php echo $get_alpha; ?>&id=<?php echo $get_id; ?>&phototype=thumb" 
                    method="post" enctype="multipart/form-data"
                >
                    Sélectionner l'image <span class="important_message">miniature de type jpg</span> à ajouter:<br>
                    <input type="file" name="fileToUpload" id="fileToUpload"><br>
                    <input type="submit" value="Upload Image" name="submit">
                </form>
            </div>
            <?php
                echo "<h5>Image(s) large(s)</h5>";
                if(count($list_images_large) > 0){
                    echo "<div class='div_list_images'>";
                    foreach($list_images_large as $link_image){
                        echo "<img src=".$link_image.">";
                    }
                    echo "</div>";
                }else{
                    echo "<p>Pas d'images</p>";
                }
                
            ?>
            <div class="div_add_image">
                <form action="index.php?p=<?php echo $url_page; ?>&action=addphoto&alpha=<?php echo $get_alpha; ?>&id=<?php echo $get_id; ?>&phototype=large" 
                    method="post" enctype="multipart/form-data"
                >
                    Sélectionner l'image <span class="important_message">large de type jpg</span> à ajouter:<br>
                    <input type="file" name="fileToUpload" id="fileToUpload"><br>
                    <input type="submit" value="Upload Image" name="submit">
                </form>
            </div>
            <?php
        }
        ?>

    </div>
</div>