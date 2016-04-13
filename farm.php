<?php
/**
 * This overwrites DOKU_CONF. Each animal gets its own configuration and data directory.
 * This can be used together with preload.php. See preload.php.dist for an example setup.
 * For more information see http://www.dokuwiki.org/farms.
 *
 * The farm directory (constant DOKU_FARMDIR) can be any directory and needs to be set.
 * Animals are direct subdirectories of the farm directory.
 * There are two different approaches:
 *  * An .htaccess based setup can use any animal directory name:
 *    http://example.org/<path_to_farm>/subdir/ will need the subdirectory '$farm/subdir/'.
 *  * A virtual host based setup needs animal directory names which have to reflect
 *    the domain name: If an animal resides in http://www.example.org:8080/mysite/test/,
 *    directories that will match range from '$farm/8080.www.example.org.mysite.test/'
 *    to a simple '$farm/domain/'.
 *
 * @author Anika Henke <anika@selfthinker.org>
 * @author Michael Klier <chi@chimeric.de>
 * @author Christopher Smith <chris@jalakai.co.uk>
 * @author virtual host part of farm_confpath() based on conf_path() from Drupal.org's /includes/bootstrap.inc
 *   (see https://github.com/drupal/drupal/blob/7.x/includes/bootstrap.inc#L537)
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */



// FIXME we later include this one directly from inc/preload
require(DOKU_INC . 'lib/plugins/farmer/DokuWikiFarmCore.php');

