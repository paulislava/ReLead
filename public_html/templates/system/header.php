<?php
global $Page;
?><!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title><?=$Page["title"]?></title>
        <link rel="icon" href="<?=HOME_URI?>/imgs/icon128x128.png" sizes="128x128" />
        <meta name="og:image" content="<?=HOME_URI?>/imgs/snippet.jpg" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,200,300,400,500,600,700,800,900&subset=latin,cyrillic" type="text/css" media="all" />
        <link rel="stylesheet" href="<?=HOME_URI?>/css/reset.css?v=<?=RESET_STYLE_VERS?>">
        <link rel="stylesheet" href="<?=HOME_URI?>/css/bootstrap.css?v=<?=BOOTSTRAP_STYLE_VERS?>">
        <link rel="stylesheet" href="<?=HOME_URI?>/css/animations.css?v=<?=ANIM_STYLE_VERS?>">   
        <link rel="stylesheet" href="<?=HOME_URI?>/css/default-style.css?v=<?=RESET_STYLE_VERS?>">
        <? if(defined("OFFICE")) : ?>
        <link rel="stylesheet" href="<?=HOME_URI?>/css/office.css?v=<?=OFFICE_STYLE_VERS?>">
        <?else:?>
        <link rel="stylesheet" href="<?=HOME_URI?>/css/style.css?v=<?=SITE_STYLE_VERS?>">
        <?endif;?>
        <script type="text/javascript" src="<?=HOME_URI?>/js/jquery.js?v=<?=JQUERY_SCRIPT_VERS?>"></script>
        <script type="text/javascript" src="<?=HOME_URI?>/js/bootstrap.js?v=<?=BOOTSTRAP_SCRIPT_VERS?>"></script>
        <script type="text/javascript" src="<?=HOME_URI?>/js/script.js?v=<?=time()?>"></script>
        
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-53294268-7"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
        
          gtag('config', 'UA-53294268-7');
        </script>

        
        <!-- Yandex.Metrika counter -->
        <script type="text/javascript" >
           (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
           m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
           (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        
           ym(56549116, "init", {
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true,
                webvisor:true
           });
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/56549116" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->
    </head>
    <body class="<?=$Page["body_class"]?> <?=$Page["id"]?> nojs">