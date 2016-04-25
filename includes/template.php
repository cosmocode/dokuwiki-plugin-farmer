<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="popup no-js">
<head>
    <meta charset="utf-8"/>
    <title>
        <?php echo $title ?>
    </title>
    <script>(function (H) {
            H.className = H.className.replace(/\bno-js\b/, 'js')
        })(document.documentElement)</script>
    <?php tpl_metaheaders() ?>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <?php echo tpl_favicon(array('favicon', 'mobile')) ?>
    <?php tpl_includeFile('meta.html') ?>
</head>

<body>
<!--[if lte IE 8 ]>
<div id="IE8"><![endif]-->
<div class="dokuwiki">
    <div class="page">
        <?php echo $body ?>
    </div>
</div>
<!--[if lte IE 8 ]></div><![endif]-->
</body>
</html>
