<?php
/**
 * English language file for farmer plugin
 *
 * @author Michael Große <grosse@cosmocode.de>
 */

// menu entry for admin plugins
$lang['menu'] = 'Farmer: Add new animal';

$lang['subdomain_helptext_injection'] = 'If you are using the subdomain based approach you need to enter the subdomain under which the new subwiki will be reachable';

//labels
$lang['preloadPHPForm'] = 'Create a new preload.php';
$lang['farm dir'] = 'farm dir';
$lang['animal configuration'] = 'Basic animal configuration';
$lang['admin password'] = 'Password for the new admin';
$lang['animal administrator'] = 'Animal administrator';
$lang['importUsers'] = 'Import all users of the master wiki to the new animal';
$lang['currentAdmin'] = 'Set the current user as admin';
$lang['newAdmin'] = 'Create new admin user "admin"';
$lang['server configuration'] = 'Server configuration';
$lang['htaccess setup'] = 'htaccess setup';
$lang['subdomain setup'] = 'Subdomain setup';
$lang['animal subdomain'] = 'Animal subdomain';
$lang['bulkSingleSwitcher'] = 'Edit a single animal or all at once?';
$lang['bulkEdit'] = 'Bulk edit all animals';
$lang['singleEdit'] = 'Edit a single animal';
$lang['bulkEditForm'] = 'Activate or deactivate a plugin in all animals';
$lang['activate'] = 'Activate';
$lang['deactivate'] = 'Deactivate';
$lang['singleEditForm'] = 'Edit the plugins of a specific animal';
$lang['submit'] = 'Submit';
$lang['reset'] = 'Reset';
$lang['js']['submit'] = $lang['submit'];
$lang['js']['reset'] = $lang['reset'];
$lang['animal name'] = 'Animal name / Wiki Title';
$lang['htaccess_basedir'] = 'Enter the path to the above-entered farm directory relativ to the server root:';

// input placeholders
$lang['js']['animalSelect'] = 'Select an animal';
$lang['js']['pluginSelect'] = 'Select a plugin';
$lang['animal name placeholder'] = 'Animal name';
$lang['animal subdomain placeholder'] = 'animal.wiki.example.com';
$lang['admin password placeholder'] = 'Password';

// success messages
$lang['animal creation success'] = 'The animal "%s" has been successfully created.';
$lang['animal creation error'] = 'There was an error while creating the animal.';
$lang['preload creation success'] = 'inc/preload.php has been succesfully created.';
$lang['preload creation error'] = 'There was an error while creating inc/preload.php.';
$lang['js']['animal ajax success'] = 'Ajax request to the new animal was successful.';
$lang['js']['preload ajax success'] = 'Ajax request to a non-existing animal was correctly served by the farmer.';

// info messages
$lang['overwrite_preload'] = 'by creating a new preload.php here, your current configuration will be overwritten';

// error messages
$lang['htaccess_basedir_missing'] = 'Please enter the <a href="https://www.dokuwiki.org/config:basedir">basedir</a>';
$lang['animalname_missing'] = 'Please enter a name for the new animal.';
$lang['animalname_invalid'] = 'The animalname may only contain alphanumeric characters and hyphens(but not as first or last character).';
$lang['animalname_preexisting'] = 'An animal with that name already exists.';
$lang['adminsetup_missing'] = 'Choose an admin for the new animal.';
$lang['adminPassword_empty'] = 'The password for the new admin account must not be empty.';
$lang['serversetup_missing'] = 'Choose either a subdomain setup and enter a valid subdomain or choose a htaccess setup.';
$lang['animalsubdomain_missing'] = 'Please enter a valid domain for the new animal.';
$lang['animalsubdomain_invalid'] = 'Please enter a valid subdomain (FQDN) without underscores.';
$lang['animalsubdomain_preexisting'] = 'An animal with that subdomain already exists.';
$lang['farmdir_missing'] = 'Please enter a directory where the animals should be stored.';
$lang['farmdir_in_dokuwiki'] = 'The farm directory must outside of the master dokuwiki.';
$lang['farmdir_uncreatable'] = 'The farm directory could not be created. Are the permissions correct?';
$lang['farmdir_unwritable'] = 'Please make sure that the webserver has write access in the farm directory';
$lang['farmdir_notEmpty'] = 'The farm directory must be empty.';
$lang['get request failure'] = 'The specified domain name could not be verified to be part of this farm setup. This is most probably a DNS or web server misconfiguration. Please refer to the farm plugin documentation on how to setup (sub-)domain farms.';
$lang['js']['preload ajax failure'] = $lang['get request failure'];
$lang['js']['animal ajax failure'] = $lang['get request failure'];
//Setup VIM: ex: et ts=4 :

