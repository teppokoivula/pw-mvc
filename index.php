<?php

/**
 * Front Controller
 * 
 * Front controller is a centralized entry point for all requests and handler
 * for most common template level tasks. Selecting the Controller and setting
 * up the View are some of it's main responsibilities.
 * 
 * This file should not be modified. Customizations affecting the entire site
 * should go to index.before.php, index.custom.php, or index.after.php instead.
 * If these files don't exist, you can create them: they are regular PHP files
 * included at specific times during the control flow of the front controller.
 * 
 * Note: the suggested naming for the front controller file is index.php, but
 * you can technically speaking rename this file if something else suits your
 * needs better. In this case the custom files should be adjusted accordingly.
 * 
 * @version 1.4.0
 * @author Teppo Koivula <teppo.koivula@gmail.com>
 * @license Mozilla Public License v2.0 http://mozilla.org/MPL/2.0/
 */

// configuration settings; if you need to customize any of these, instead of
// making changes here, copy this array to config.php as $config->mvc
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
    'paths' => array(
        'lib' => $config->paths->templates . "lib/",
        'views' => $config->paths->templates . "views/",
        'scripts' => $config->paths->templates . "views/scripts/",
        'layouts' => $config->paths->templates . "views/layouts/",
        'partials' => $config->paths->templates . "views/partials/",
        'controllers' => $config->paths->templates . "controllers/",
    ),
    'urls' => array(
        'static' => $config->urls->templates . "static/",
    )
);

// combine default settings with custom ones and define shortcuts for later use
$config->mvc = is_array($config->mvc) ? array_merge($config_defaults, $config->mvc) : $config_defaults;
$config->urls->static = $config->mvc['urls']['static'];
$front_controller = basename(__FILE__, '.php');
$paths = (object) $config->mvc['paths'];
$ext = "." . $config->templateExtension;

// include a file containing custom front controller logic; the contents of
// this file are executed before anything else, making this a good place to
// perform things that don't fit the normal program flow (like redirects)
if (is_file($config->paths->templates . $front_controller . ".before.php")) {
    include $config->paths->templates . $front_controller . ".before.php";
    if ($this->halt) return $this->halt();
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

// fetch required classes and files
require_once $paths->lib . "ViewPlaceholders.php";
require_once $paths->lib . "Functions.php";
require_once $paths->lib . "Hooks.php";

// set PHP include path
$include_paths = array($paths->views);
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
$view->script = $page->view();
$view->partials = getFilesRecursive($paths->partials . "*", $ext);
$view->placeholders = new ViewPlaceholders($page, $paths->scripts, $ext);

// include a file containing custom front controller logic; since this file is
// executed right before dispatching the template controller, within it you'll
// have access to the View component
if (is_file($config->paths->templates . $front_controller . ".custom.php")) {
    include $config->paths->templates . $front_controller . ".custom.php";
    if ($this->halt) return $this->halt();
}

// initialise the Controller; since this template-specific component isn't
// required, we'll start by checking if it even exists
if (is_file($paths->controllers . $page->template . $ext)) {
    include $paths->controllers . $page->template . $ext;
    if ($this->halt) return $this->halt();
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
    } else {
        $get_view = $input->get->view;
    }
}
$view->script = basename($view->script ?: ($page->view() ?: ($get_view ?: 'default')));
if ($view->script != "default" && !is_file($paths->scripts . $page->template . "/" . $view->script . $ext)) {
    $view->script = "default";
}
if ($view->script != "default" || is_file($paths->scripts . $page->template . "/" . $view->script . $ext)) {
    $view->filename = $paths->scripts . $page->template . "/" . $view->script . $ext;
    if ($page->_pwmvc_context != "placeholder") {
        if ($view->script != "default" && !$view->allow_cache) {
            // not using the default view script, disable page cache
            $session->PageRenderNoCachePage = $page->id;
        } else if ($session->PageRenderNoCachePage == $page->id) {
            // make sure that page cache isn't skipped unnecessarily
            $session->remove("PageRenderNoCachePage");
        }
    }
}

// if view script and/or layout is defined, render the page
$out = null;
if ($view->filename || $view->layout) {
    $out = $view->render();
    if ($filename = basename($view->layout)) {
        // layouts make it possible to define a common base structure for
        // multiple otherwise separate templates and view scripts (DRY)
        $view->filename = $paths->layouts . $filename . $ext;
        if (!$view->placeholders->content) {
            $view->placeholders->content = $out;
        }
        $out = $view->render();
    }
}

// include a file containing custom front controller logic; the contents of
// this file are executed right before content is displayed, which makes it
// possible to make final adjustments to (or based on) the output itself
if (is_file($config->paths->templates . $front_controller . ".after.php")) {
    include $config->paths->templates . $front_controller . ".after.php";
    if ($this->halt) return $this->halt();
}

// final step: output rendered markup
if (!is_null($out)) echo $out;
