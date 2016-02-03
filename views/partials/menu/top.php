<?php
/**
 * Partial file for simple top / main menu
 * 
 * Partials are simply include files shared by multiple templates. Note that
 * the top menu could as well be a part of default layout; in this case it's
 * separated into a partial for demonstration purposes.
 *
 */
?>
<nav>
    <ul>
        <?php foreach ($pages->get(1)->and($pages->get(1)->children) as $item): ?>
        <li><a href="<?= $item->url ?>"><?= $item->title ?></a></li>
        <?php endforeach; ?>
    </ul>
</nav>
