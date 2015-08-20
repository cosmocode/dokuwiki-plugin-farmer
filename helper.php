<?php
/**
 * DokuWiki Plugin farmer (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_farmer extends DokuWiki_Plugin {

    /**
     * Copy a file, or recursively copy a folder and its contents. Adapted for DokuWiki.
     *
     * @todo: needs tests
     *
     * @author      Aidan Lister <aidan@php.net>
     * @author      Michael Große <grosse@cosmocode.de>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     *
     * @param       string $source Source path
     * @param       string $destination      Destination path
     *
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    function io_copyDir($source, $destination) {
        if (is_link($source)) {
            io_lock($destination);
            $result=symlink(readlink($source), $destination);
            io_unlock($destination);
            return $result;
        }

        if (is_file($source)) {
            io_lock($destination);
            $result=copy($source, $destination);
            io_unlock($destination);
            return $result;
        }

        if (!is_dir($destination)) {
            io_mkdir_p($destination);
        }

        $dir = dir($source);
        while (false !== ($entry = $dir->read())) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // recurse into directories
            $this->io_copyDir("$source/$entry", "$destination/$entry");
        }

        $dir->close();
        return true;
    }



    public function getAllPlugins() {
        $dir = dir(DOKU_PLUGIN);
        $plugins = array();
        while (false !== ($entry = $dir->read())) {
            if($entry == '.' || $entry == '..' || $entry == 'testing') {
                continue;
            }
            if (!is_dir(DOKU_PLUGIN ."/$entry")) {
                continue;
            }
            $plugins[] = $entry;
        }
        sort($plugins);
        return $plugins;
    }

    public function getAllAnimals() {
        $animals = array();

        $dir = dir(DOKU_FARMDIR);
        while (false !== ($entry = $dir->read())) {
            if ($entry == '.' || $entry == '..' || $entry == '_animal') {
                continue;
            }
            $animals[] = $entry;
        }
        $dir->close();
        return $animals;
    }

}
