<!-- Page d'accueil -->

<div id='search' class='u-full-width'>
    <div id="trail" class="container row">
        <ul>
            <li>Vous êtes ici :</li>
            <li>Page d'accueil</li>
        </ul>
    </div>
</div>

<!-- Affichage des produits -->
<section class="container">
    <?php
        // Les produits vont être affichés par ligne de 3
        if($list_articles_public == []){
            echo "<div class='row'>".$msg_not_product."</div>";
        } else{
        
            $counter_row = 0;
            $counter_product = 0;
            foreach($list_articles_public as $article_public){
                $counter_product++;
                if($counter_row % 3 === 0){
                    echo "<div class='row'>";
                }
    ?>
                <article class="pres_product four columns border">
                    <!-- Affichage de l'image principale du produit -->
                    <div class="thumb">
                        <a href="./?p=detail&id=<?php echo $article_public["ad_id"]?>" title="<?php echo $article_public["ad_title"]?>">
                            <span class="rollover"><i>+</i></span>
                            <?php
                                if(file_exists('upload/thumb/thumb_'.$article_public["ad_id"].'-1.jpg')){
                                    echo "<img src='upload/thumb/thumb_$article_public[ad_id]-1.jpg' alt='' />";
                                }
                                
                            ?>

                        </a>
                    </div>

                    <!-- Affichage des données principales du produit hors description -->
                    <header>
                        <h4><a href="./?p=detail&id=<?php echo $article_public["ad_id"]?>" title="<?php echo $article_public["ad_title"]?>"><?php echo $article_public["ad_title"]?></a></h4>
                        <div class="subheader">
                            <span class="fa fa-bars"></span> <a href="" title=""></a>
                            <span class="separator">|</span>
                            <span class="fa fa-pencil"></span> 
                            <?php
                                if($article_public["firstname"] && $article_public["lastname"]){
                                    echo "<a href='#' title=''>".$article_public["firstname"]." ". $article_public["lastname"]."</a>";
                                } else{
                                    echo "<small style='opacity:.5;'>- Non précisé -</small>";
                                }
                                
                            ?>
                            <span class="separator">|</span>
                            <span class="fa fa-building-o"></span> 

                            <?php
                                if($article_public["manufacturer"]){
                                    echo "<a href='#' title=''>".$article_public["manufacturer"]."</a>";
                                } else{
                                    echo "<small style='opacity:.5;'>- Non précisé -</small>";
                                }
                                
                            ?>
                        </div>
                    </header>
                    <!-- Affichage de la courte description du produit
                                le "&#8239;" est un espace insécable
                    -->
                    <div class="une_txt">
                        <p>
                            <?php
                                if($article_public["ad_description_detail"]){
                                    if(strlen($article_public["ad_description_detail"]) >100){
                                        echo cutLengthText($article_public["ad_description_detail"], 100);
                                        echo "&#8239;<a href='./?p=detail&id=".$article_public["ad_id"]."' title=''>[...]</a>";
                                    } else{
                                        echo $article_public["ad_description_detail"];
                                    }
                                } else{
                                    echo "<a href='./?p=detail&id=".$article_public["ad_id"]."' title=''>Plus de détails</a>";
                                }
                                
                            ?>
                            <!-- Affichage du prix du produit -->
                            <b><?php echo number_format($article_public["price"], 2, ',', '.');?> €</b>
                        </p>
                    </div>
                </article>
    <?php
            $counter_row++;
            // La dernière page n'aura peut-être pas une ligne de produits complète, 
            // mais il faut fermer la "div row" au dernier produit
            // On va vérifier si on est à la dernière page et au dernier produit,
            // si c'est le cas on ferme la div row
            if($counter_row % 3 === 0 || 
                ($get_page == $amount_pages_gross && $counter_product == ($amount_products % $amount_per_page))){
                echo "</div>";
            }
        }
        }
    ?>
    <!-- début de la pagination -->
    <br /><br />
    <ul class='pagination'>
        <?php
            echo $lower_text_pagination;
            echo $lower_dots_pagination;

            for($i = $lower_limit_page; $i <= $higher_limit_page; $i++){
                if($i == $get_page){
                    echo "<li><a href='index.php?page=".$i."' class='active'>".$i."</a></li>";
                } else{
                    echo "<li><a href='index.php?page=".$i."' class=''>".$i."</a></li>";
                }
            }
            echo $higher_dots_pagination;
            echo $higher_text_pagination;
        ?>
    </ul>
    <!-- fin de la pagination -->

</section>