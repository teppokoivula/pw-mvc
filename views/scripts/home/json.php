<?php

/**
 * An alternative view script for the 'home' template
 * 
 * This view script renders predefined page properties as JSON. This view can
 * be triggered by specifying view programmatically or by requesting the page
 * with GET param 'view':
 * 
 * Option A: (via $view object) $view->script = 'json';
 * Option B: http://www.example.com/?view=json
 * Option C: (via $page object) $page->view('json');
 *
 */

// skip layout for this view
$this->layout = null;

// fill $data array with page properties
$data = array(
    'id' => $page->id,
    'name' => $page->name,
    'url' => $page->url,
    'title' => $page->title,
    'body' => $page->body,
    'sidebar' => $page->sidebar,
);

// output $data array as JSON string
header('Content-Type: application/json');
echo json_encode($data);
