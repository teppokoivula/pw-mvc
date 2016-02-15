<?php

/**
 * Front Controller
 * 
 * Front controller is a centralized entry point for all requests and handler
 * for most common template level tasks. Selecting the Controller and setting
 * up the View are some of it's main responsibilities.
 * 
 * This file should not be modified. Customizations affecting the entire site
 * should go to index.custom.php instead. If said file doesn't exist yet, you
 * can create it: it's a regular PHP file included near the end of this file.
 * 
 * @version 1.2.0
 * @author Teppo Koivula <teppo.koivula@gmail.com>
 * @license Mozilla Public License v2.0 http://mozilla.org/MPL/2.0/
 */

// configuration settings; if you need to customize these, copy this array to
// config.php as config setting 'mvc' ($config->mvc)
$config_defaults = array(
    'include_paths' => array(
        // '/path/to/shared/libraries/',
    ),
    'redirect_fields' => array(
        // 'redirect_to_url',
        // 'redirect_to_page' => array(
        //     'property' => 'url',
        //     'permanent' => true,
        // ),
    ),
    'allow_get_view' => true,
    // 'allow_get_view' => array(
    //     'home' => array(
    //         'json',
    //         'rss',
    //     ),
    //     'json',
    // ),
);
$config->mvc = is_array($config->mvc) ? array_merge($config_defaults, $config->mvc) : $config_defaults;

// include a file containing custom front controller logic; the contents of
// this file are executed before anything else, making this a good place to
// perform things that don't fit the normal program flow (like redirects)
if (is_file("{$config->paths->templates}index.before.php")) {
    include "{$config->paths->templates}index.before.php";
}

// look for redirect fields from config settings; if present, check if the page
// has a value in one of these and if a redirect should be triggered
if (count($config->mvc['redirect_fields'])) {
    foreach ($config->mvc['redirect_fields'] as $field => $options) {
        if (is_int($field) && is_string($options)) $field = $options;
        if ($page->$field) {
            $url = $page->$field;
            $permanent = false;
            if (is_array($options)) {
                if (isset($options['property'])) $url = $url->$options['property'];
                if (isset($options['permanent'])) $permanent = (bool) $options['permanent'];
            }
            if (is_string($url) && $url != $page->url && $sanitizer->url($url)) {
                $session->redirect($url, $permanent);
            }
        }
    }
}

require_once "{$config->paths->templates}/lib/ViewPlaceholders.php";
require_once "{$config->paths->templates}/lib/Functions.php";
require_once "{$config->paths->templates}/lib/Hooks.php";

// initialise variables
$ext = ".{$config->templateExtension}";
$views = "{$config->paths->templates}views/";
$scripts = "{$views}scripts/{$page->template}/";
$controllers = "{$config->paths->templates}controllers/";
$config->urls->static = "{$config->urls->templates}static/";

// set PHP include path
$include_paths = array($views);
if (count($config->mvc['include_paths'])) {
    $include_paths = array_merge($include_paths, $config->mvc['include_paths']);
}
if (strpos(get_include_path(), $include_paths[0]) === false) {
    set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $include_paths));
}

// initialise the View
$view = new TemplateFile;
$this->wire('view', $view);
$view->layout = $page->layout() === null ? 'default' : $page->layout();
$view->script = $page->view() === null ? null : $page->view();
$view->partials = getFilesRecursive("{$views}partials/*", $ext);
$view->placeholders = new ViewPlaceholders($page, $scripts, $ext);

// include a file containing custom front controller logic; the contents of
// this file are executed right before dispatching the template controller,
// which means that this file already has access to the View component etc.
if (is_file("{$config->paths->templates}index.custom.php")) {
    include "{$config->paths->templates}index.custom.php";
}

// initialise the Controller; since this template-specific component isn't
// required, we'll first have to check if it exists at all
if (is_file("{$controllers}{$page->template}{$ext}")) {
    include "{$controllers}{$page->template}{$ext}";
}

// choose a view script; default value is 'default', but view() method of the
// $page object or GET param 'view' can also be used to set the view script
$get_view = null;
if ($input->get->view && $allow_get_view = $config->mvc['allow_get_view']) {
    if (is_array($allow_get_view)) {
        // allowing *any* view script to be used via a GET param might not be
        // appropriate; using a whitelist allows us to configure valid values
        foreach ($allow_get_view as $template => $value) {
            if (is_string($template) && is_array($value) && $page->template == $template) {
                $get_view = in_array($input->get->view, $value) ? $input->get->view : null;
                break;
            } else if (is_int($template) && is_string($value) && $input->get->view == $value) {
                $get_view = $input->get->view;
                break;
            }
        }
    } else if ($allow_get_view) {
        $get_view = $input->get->view;
    }
}
$view->script = basename($view->script ?: ($page->view() ?: ($get_view ?: 'default')));
if ($view->script != "default" && !is_file("{$scripts}{$view->script}{$ext}")) {
    $view->script = "default";
}
if ($view->script != "default" || is_file("{$scripts}{$view->script}{$ext}")) {
    $view->filename = "{$scripts}{$view->script}{$ext}";
    if ($view->script != "default") {
        // not using the default view script, disable page cache
        $session->PageRenderNoCachePage = $page->id;
    } else if ($session->PageRenderNoCachePage == $page->id) {
        // make sure that page cache isn't skipped unnecessarily
        $session->remove("PageRenderNoCachePage");
    }
}

// if view script and/or layout is defined, render the page
$out = null;
if ($view->filename || $view->layout) {
    $out = $view->render();
    if ($filename = basename($view->layout)) {
        // layouts make it possible to define a common base structure for
        // multiple otherwise separate templates and view scripts (DRY)
        $view->filename = "{$views}layouts/{$filename}{$ext}";
        if (!$view->placeholders->content) {
            $view->placeholders->content = $out;
        }
        $out = $view->render();
    }
}

// include a file containing custom front controller logic; the contents of
// this file are executed right before content is displayed, which makes it
// possible to make final adjustments to (or based on) the output itself
if (is_file("{$config->paths->templates}index.after.php")) {
    include "{$config->paths->templates}index.after.php";
}

// final step: output rendered markup
if (!is_null($out)) echo $out;
