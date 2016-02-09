<?php

/**
 * Front Controller
 * 
 * Front controller is an entry point for requests and handler for most
 * common template level tasks. Selecting the Controller and setting up
 * the View are some of it's main responsibilities.
 * 
 * @author Teppo Koivula <teppo.koivula@gmail.com>
 * @version 1.0.2
 * @license Mozilla Public License v2.0 http://mozilla.org/MPL/2.0/
 */

require_once "{$config->paths->templates}/lib/ViewPlaceholders.php";
require_once "{$config->paths->templates}/lib/Functions.php";
require_once "{$config->paths->templates}/lib/Hooks.php";

// initialise variables
$ext = ".{$config->templateExtension}";
$views = "{$config->paths->templates}views/";
$scripts = "{$views}scripts/{$page->template}/";
$controllers = "{$config->paths->templates}controllers/";
$config->urls->static = "{$config->urls->templates}static/";

// fetch a list of available partials
$partials = getFilesRecursive("{$views}partials/*", $ext);

// initialise placeholders
$placeholders = new ViewPlaceholders($page, $scripts, $ext);

// initialise the Layout
$layout = new TemplateFile;
$layout->partials = $partials;
$layout->placeholders = $placeholders;

// initialise the View
$view = new TemplateFile;
$view->set('layout', $page->layout() === null ? 'default' : $page->layout());
$view->set('view', $page->view() === null ? null : $page->view());
$view->partials = $partials;
$view->placeholders = $placeholders;

// initialise the Controller; since this template-specific component isn't
// required, we'll first have to check if it exists at all
if (is_file("{$controllers}{$page->template}{$ext}")) {
    include "{$controllers}{$page->template}{$ext}";
}

// choose a view script; default value is 'index', but view() method of the
// $page object or GET param 'view' can also be used to set the view script
if ($view->view && is_file("{$scripts}{$view->view}{$ext}")) {
    $view->filename = "{$scripts}{$view->view}{$ext}";
} else {
    $filename = basename($input->get->view ?: ($page->view() ?: 'index'));
    if (is_file("{$scripts}{$filename}{$ext}")) {
        $view->filename = "{$scripts}{$filename}{$ext}";
        if ($filename != "index") {
            // not using the default view script, disable page cache
            $session->PageRenderNoCachePage = $page->id;
        } else if ($session->PageRenderNoCachePage == $page->id) {
            // make sure that page cache isn't skipped unnecessarily
            $session->remove("PageRenderNoCachePage");
        }
    }
}

// include file containing custom front controller logic; the intention here
// is to keep the Front Controller itself intact and easy to keep up to date
if (is_file("index.custom.php")) include "index.custom.php";

// if view script and/or layout is defined, render the page
if ($view->filename || $view->layout) {
    $content = $view->render();
    if ($filename = basename($view->layout)) {
        // layouts make it possible to define a common base structure for
        // multiple otherwise separate templates and view scripts (DRY)
        $layout->filename = "{$views}layouts/{$filename}{$ext}";
        $layout->partials = $view->partials;
        if (!$layout->placeholders->content) {
            $layout->placeholders->content = $content;
        }
        $content = $layout->render();
    }
    echo $content;
}
