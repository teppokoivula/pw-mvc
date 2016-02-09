<?php

/**
 * Hooks file
 * 
 * This file is intended for various hooks that make it easier to work with
 * template level stuff: shortcuts for getting and setting properties, etc.
 *
 * @version 1.0.0
 * @author Teppo Koivula <teppo.koivula@gmail.com>
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License, version 2
 */

/**
 * Helper method for getting or setting page layout
 * 
 * Example: <?= $page->layout('default')->render() ?>
 *
 */
wire()->addHook('Page::layout', function(HookEvent $event) {
    if (isset($event->arguments[0])) {
        $event->object->_layout = $event->arguments[0];
        $event->return = $event->object;
    } else {
        $event->return = $event->object->_layout;
    }
});

/**
 * Helper method for getting or setting page view
 * 
 * Example: <?= $page->view('json')->render() ?>
 *
 */
wire()->addHook('Page::view', function(HookEvent $event) {
    if (isset($event->arguments[0])) {
        $event->object->_view = $event->arguments[0];
        $event->return = $event->object;
    } else {
        $event->return = $event->object->_view;
    }
});
