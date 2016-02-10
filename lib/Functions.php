<?php

/**
 * Functions file
 * 
 * This file is for general purpose functions required by the front controller
 * and any other output-related features.
 *
 * @version 1.0.3
 * @author Teppo Koivula <teppo.koivula@gmail.com>
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License, version 2
 */

/**
 * Fetch a list of files recursively
 *
 * @param string $path Base directory
 * @param string $ext File extension
 * @return stdClass
 */
function getFilesRecursive($path, $ext) {
    $files = array();
    foreach (glob($path) as $file) {
        $name = basename($file);
        if (strpos($name, ".") === 0) continue;
        if (is_dir($file)) {
            $files[$name] = getFilesRecursive("{$file}/*", $ext);
        } else if (strrpos($name, $ext) === strlen($name)-strlen($ext)) {
            $files[substr($name, 0, strrpos($name, "."))] = $file;
        }
    }
    return (object) $files;
}
