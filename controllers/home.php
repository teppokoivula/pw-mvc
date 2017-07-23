<?php

/**
 * Controller file for the 'home' template
 * 
 * If a template requires code that is not strictly view related, you can add
 * a controller file for it. Generally speaking it's a good idea to keep code
 * out of your views (separation of concerns).
 * 
 */

$view->placeholders->head = "<link rel='stylesheet' type='text/css' href='{$config->urls->static}css/home.css' />";
