<?php

/**
 * Controller file for the 'home' template
 * 
 * If any of your templates require template-level program logic, you can add
 * controller files for them. It's generally speaking a good idea to keep any
 * complex logic out of your views (separation of concerns).
 * 
 */

$view->placeholders->head = "<link rel='stylesheet' type='text/css' href='{$config->urls->static}css/home.css' />";
