<?php

/**
 * Hooks file
 * 
 * This file is intended for various hooks that make it easier to work with
 * template level stuff: shortcuts for getting and setting properties, etc.
 *
 * @version 1.0.1
 * @author Teppo Koivula <teppo.koivula@gmail.com>
 * @license Mozilla Public License v2.0 http://mozilla.org/MPL/2.0/
 */

/**
 * Helper method for getting or setting page layout
 * 
 * Example: <?= $page->layout('default')->render() ?>
 *
 */
wire()->addHook('Page::layout', function(HookEvent $event) {
    if (empty($event->arguments)) {
        $event->return = $event->object->_pwmvc_layout;
    } else {
        $event->object->_pwmvc_layout = $event->arguments[0];
        $event->return = $event->object;
    }
});

/**
 * Helper method for getting or setting page view
 * 
 * Example: <?= $page->view('json')->render() ?>
 *
 */
wire()->addHook('Page::view', function(HookEvent $event) {
    if (empty($event->arguments)) {
        $event->return = $event->object->_pwmvc_view;
    } else {
        $event->object->_pwmvc_view = $event->arguments[0];
        $event->return = $event->object;
    }
});
