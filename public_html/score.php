<?php
require_once 'init.php';
$Page["title"] = "Мой рейтинг";
$Page["id"] = "score-page";

$base_id = intval($_GET["b"]);
$Page["body_class"] = "body-center base-{$base_id}";
$code = trim($_GET["c"]);
$config["consultant"] = false;
$ref = $mdb->where($mdb->scores, array("code" => $code, "base_id" => $base_id));
pil_header();
?>
<style>
    :root {
        --score-back: white;
        --score-1: #313131;
        --score-2: yellow;
        --score-3: #da2b87;
        --score-4: white;
        --score-5: #225ca9;
        --score-6: #2669bf;
    }

    /* Score page */
    main>.row {
        flex-flow: row;
    }

    body.score-page {
        background: var(--score-back);
        color: var(--score-1);
    }

    .score-page main {
        width: 80%;
        max-width: 40em;
    }

    .score-page h1 {
        margin-bottom: 1.5rem;
        font-size: 1.77em;
        color: var(--score-5);
    }

    .score-block {
        max-width: 20em;
        margin: auto;
        font-size: 3em;
        font-weight: 400;
        border: 2px solid var(--score-1);
        padding: 0.5rem;
        box-shadow: 2px 2px 5px var(--score-1);
    }

    .score-block {
        background: var(--score-3);
        border: none;
        box-shadow: 2px 2px 5px var(--score-3);
        color: var(--score-4);
        margin-bottom: 1rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
    }

    .score-block.position-1 {
        border-color: var(--score-2);
        box-shadow: 2px 2px 5px var(--score-2);
        color: var(--score-2);
    }

    .score-page h2 {
        font-size: 1.5em;
    }

    .score-block small {
        font-weight: 200;
        margin-left: 0.3rem;
        font-size: 0.33em;
    }

    .score-link {
        margin-top: 1rem;
        padding: .5rem 1.5rem;
        background-color: var(--score-5);
    }

    .score-link:hover {
        background-color: var(--score-6);
    }

    #link-block {
        font-size: 1.2em;
        line-height: 2.5;
        margin-bottom: 0.3rem;
    }

    #link-block:hover,
    #link-block:active,
    #link-block:focus,
    #link-block:visited {
        color: var(--score-4);
        text-decoration: none;
    }

    .share-btn {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        background-repeat: no-repeat;
        background-size: contain;
        background-position: center;
        border-radius: 20%;
    }

    .share-buttons {
        margin: 1rem 0;
        justify-content: center;
        gap: 1rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, 2rem);
    }

    .share-vk {
        background-image: url(./imgs/icons/vk.svg);
    }

    .share-facebook {
        background-image: url(./imgs/icons/facebook.svg);
    }

    .share-twitter {
        background-image: url(./imgs/icons/twitter-3.svg);
        background-color: #2daae1;
    }

    .share-whatsapp {
        background-image: url(./imgs/icons/whatsapp.svg);
    }


    @media screen and (max-width: 1200px) {
        main>.row {
            flex-flow: column;
        }

        .score-page h2 {
            font-size: 1.3em;
        }

        .score-page main {
            min-width: 80%;
        }

        .score-link {
            font-size: 1.3em;
            min-width: 70%;
        }
    }
</style>
<main>
    <h1>Мой рейтинг</h1>
    <? if ($ref) :
        $base = $mdb->where($mdb->bases, array("id" => $base_id));
        $count =  $mdb->get_var("SELECT COUNT(*) FROM `{$mdb->referrers}` WHERE `base_id` = '{$base_id}'");
        $position = $mdb->get_var("SELECT COUNT(*) FROM `{$mdb->scores}` WHERE `base_id` = '{$base_id}' AND `score` > {$ref->score}") + 1;
        $first_score = $mdb->get_var("SELECT MAX(`score`) FROM `{$mdb->scores}` WHERE `base_id` = '{$base_id}'");
        $site = $mdb->where($mdb->sites, array("id" => $base->site_id));
        $diff = $first_score - $ref->score;
        $link = "https://{$site->domain}/?rld_ref={$code}";
        $share_text = $base->share_text;

        $link_encode = urlencode($link);
        $share_text_encode = urlencode($share_text);
        $share_full_text = urlencode($share_text . "\n" . $link);
        $share_btns = [];
        $share_btns[] = array(
            "type" => "vk",
            "title" => "Поделиться ВК",
            "url" => "https://vk.com/share.php?url={$link_encode}&title={$share_text_encode}"
        );
        $share_btns[] = array(
            "type" => "facebook",
            "title" => "Поделиться на Facebook",
            "url" => "https://www.facebook.com/sharer/sharer.php?u={$link_encode}&t={$share_text_encode}"
        );
        $share_btns[] = array(
            "type" => "twitter",
            "title" => "Поделиться в Twitter",
            "url" => "https://twitter.com/intent/tweet?url={$link_encode}&text={$share_text_encode}"
        );

        $share_btns[] = array(
            "type" => "whatsapp",
            "title" => "Поделиться в WhatsApp",
            "url" => "https://wa.me/?text={$share_full_text}",
        );
    ?>
        <div class="score-block" id="score-block"><?= $ref->score ?><small>очков</small></div>
        <div class="row">
            <div class="col">
                <h2>Место в рейтинге</h2>
                <div class="score-block position-<?= $position ?>" id="place-block"><span id="position"><?= $position ?></span><?php if ($position > 1) : ?><small>из <span id="score-all"><?= $count ?></span></small><? endif; ?></div>

                <? if ($position > 1) : ?>
                    <h2>До первого места</h2>
                    <div class="score-block" id="diff-block"><?= $diff ?><small>очков</small></div>
                <? endif; ?>
            </div>
            <div class="col">
                <h2>Реферальная ссылка</h2>
                <a class="score-block" id="link-block" href="<?= $link ?>" target="_blank"><?= $link ?></a>
                <small>Поделитесь этой ссылкой с друзьями.</small>
                <div class="share-buttons">
                    <? foreach ($share_btns as $share_btn) : ?>
                        <a class="share-btn share-<?= $share_btn["type"] ?>" <?= html_attrs($share_btn["attrs"]) ?> target="_blank" href="<?= $share_btn["url"] ?>"></a>
                    <? endforeach; ?>
                </div>
            </div>
        <? else : ?>
            <div class="not-found">
                Произошла ошибка :(<br>
                Возможно, ссылка недействительна или устарела.
            </div>
        <? endif; ?>
</main>
<? pil_footer(); ?>