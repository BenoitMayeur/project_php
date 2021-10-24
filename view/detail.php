<!-- 
    Page de détails d'un produit, affichée sur base d'un ID
-->

<div id='search' class='u-full-width'>
    <div id="trail" class="container row">
        <ul>
            <li>Vous êtes ici :</li>
            <li><a href='#' title='<?php echo $product_level_1 ?>'><?php echo $product_level_1 ?></a></li>
            <li><a href='#' title='<?php echo $product_level_2 ?>'><?php echo $product_level_2 ?></a></li>
            <li><?php echo $product_title ?></li>
        </ul>
    </div>
</div>
<!-- 
    Normalement les données envoyées par la DB ne comportent que les produits
    qui peuvent être montrés au client, mais on prévoit une sécurité en plus
    avec une condition qui filtre les données pour voir si elles peuvent être visibles
-->
<?php 
    if($product_visible){
?>

    <section id="photostack-1" class="photostack photostack-start u-full-width">
        <div>
            <?php
                foreach($list_images_thumb as $image_thumb){
            ?>
                <figure>
                    <img src="<?php echo $image_thumb?>" alt=""/>
                    <figcaption>
                        <h2 class="photostack-title"><?php echo $product_title?></h2>
                    </figcaption>
                </figure>
            <?php
                }
            ?>

        </div>
    </section>

    <section class="container" id="detail_ad">
        <h1><?php echo $product_title ?></h1>
        <p id="short-description">
            <?php 
                // La fonction "nl2br()" ajoutait des espaces en trop et en retirait d'autres, 
                // j'ai dû utiliser une fonction str_replace() à la place
                echo str_replace("\r\n", "<br /><br />", $product_description); 
            ?>
        </p>
        <p id="long-description">
        
            <?php 
                // La fonction "nl2br()" ajoutait des espaces en trop et en retirait d'autres, 
                // j'ai dû utiliser une fonction str_replace() à la place
                echo str_replace("\r\n", "<br /><br />", $product_description_detail); 
            ?>
        </p>
    </section>
<!-- 
    Ce message s'affiche si un client veut voir un produit qu'il ne peut pas voir.
-->
<?php 
    }else{
?>
        <section class="container" id="detail_ad">
            <p id="short-description">
                <?php echo $msg_redirect; ?>
            </p>
        </section>
<?php
    }
?>

<style>
    #n_preview{
        position: fixed;
        background: rgba(0, 0, 0, .9);
        width: 100%;
        height: 100%;
        display: block;
        top: 0;
        z-index: 999;
        text-align: center;
    }
    #n_preview img{
        height: 80%;
        max-width: 80%;
        width: auto;
        margin-top: 5%;
    }
    #n_preview #closeAppend{
        color: #fff;
        position: fixed;
        top: 20px;
        right: 10px;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
    }
    #n_preview #closeAppend i{
        font-style: normal;
    }
    #n_preview #closeAppend:hover i{
        color:#AAD5DF;
    }
    #n_preview span#nextAppend,
    #n_preview span#previousAppend{
        color: #fff;
        position: fixed;
        top: 45%;
        right: 10px;
        font-size: 20px;
        font-weight: normal;
        text-align: right;
        cursor: pointer;
        width: 120px;
        padding-top: 50px;
        padding-bottom: 50px;
    }
    #n_preview span#previousAppend{
        left: 10px;
        text-align: left;
    }
    #n_preview span#nextAppend i,
    #n_preview span#previousAppend i{
        font-style: normal;
    }
    #n_preview span#nextAppend:hover i,
    #n_preview span#previousAppend:hover i{
        color:#AAD5DF;
    }
</style>
<script>
    $(document).ready(function(){
        $('#photostack-1 figure img').on('click', function(event){
            var src = $(this).attr("src").split('_');
            var src_large = "upload/large/"+src[1];
            var qty_elt = $('#photostack-1 figure img').length;
            var src_id = src[1].split('-')[0];
            var src_position = src[1].split('-')[1].split('.')[0];

            $('<div>', {
                id:  'n_preview',
                html: '<img src="'+src_large+'" />'
            }).appendTo('body');

            $('<span>', {
                id:  'closeAppend',
                html: '[<i>x</i>] close',
                onclick: 'n_preview_hide()'
            }).appendTo('#n_preview');

            $('<span>', {
                id:  'nextAppend',
                html: 'next <i>&#10097;</i>',
                onclick: 'n_preview_next()'
            }).appendTo('#n_preview');

            $('<span>', {
                id:  'previousAppend',
                html: '<i>&#10096;</i> previous',
                onclick: 'n_preview_previous()'
            }).appendTo('#n_preview');

            $('<input>', {
                id:  'n_preview_qty',
                value: qty_elt,
                type: 'hidden'
            }).appendTo('#n_preview');

            $('<input>', {
                id:  'n_preview_id',
                value: src_id,
                type: 'hidden'
            }).appendTo('#n_preview');

            $('<input>', {
                id:  'n_preview_position',
                value: src_position,
                type: 'hidden'
            }).appendTo('#n_preview');

            //alert(qty_elt);
        });
    });
    function n_preview_hide(){
        $('#n_preview').remove();
    }
    function n_preview_next(){
        var p = parseInt($('#n_preview_position').val());
        var id = $('#n_preview_id').val();
        var max = parseInt($('#n_preview_qty').val());

        var new_val = p < max ? p+1 : 1;

        $("#n_preview img").attr("src", "upload/large/"+id+"-"+new_val+".jpg");

        $('#n_preview_position').val(new_val);
    }
    function n_preview_previous(){
        var p = parseInt($('#n_preview_position').val());
        var id = $('#n_preview_id').val();
        var max = parseInt($('#n_preview_qty').val());

        var new_val = p > 1 ? p-1 : max;

        $("#n_preview img").attr("src", "upload/large/"+id+"-"+new_val+".jpg");

        $('#n_preview_position').val(new_val);
    }
</script>
