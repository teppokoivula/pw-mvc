<?php

/**
 * Functions file
 * 
 * This file is for general purpose functions required by the front controller
 * and any other output-related features.
 *
 * @author Teppo Koivula <teppo.koivula@gmail.com>
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License, version 2
 */

/**
 * Fetch a list of available partials
 *
 * @param string $path Partials directory
 * @param string $ext Template file extension
 * @return stdClass
 */
function getPartials($path, $ext) {
    $partials = array();
    foreach (glob($path) as $partial) {
        $name = basename($partial);
        if (is_dir($partial)) {
            $partials[$name] = getPartials("{$partial}/*", $ext);
        } else if (strrpos($name, $ext) === strlen($name)-strlen($ext)) {
            $partials[substr($name, 0, strrpos($name, "."))] = $partial;
        }
    }
    return (object) $partials;
}
