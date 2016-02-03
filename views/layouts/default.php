<?php
/**
 * Layout file with predefined placeholder slots for 'head' and 'content'
 * 
 * Each layout is intended for use by multiple templates. Content intended for
 * the placeholder slots can be sent from view to layout via the $placeholders
 * object, but also by defining view scripts with names matching those of the
 * placeholder slots:
 * 
 * Option A: $view->placeholders->head = '<style> ... </style>';
 * Option B: /site/templates/views/scripts/home/head.php
 * 
 */
?>
<html>
    <head>
        <title><?= $page->title ?> - <?= $site_name ?></title>
        <link rel="stylesheet" type="text/css" href="<?= $config->urls->templates ?>static/css/style.css" />
        <?= $placeholders->head ?>
    </head>
    <body>
        <?php include $partials->menu->top ?>
        <h1><?= $page->title ?></h1>
        <?= $placeholders->content ?>
    </body>
</html>
