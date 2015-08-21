<?php
/**
 * English language file for farmer plugin
 *
 * @author Michael Große <grosse@cosmocode.de>
 */

// menu entry for admin plugins
$lang['menu'] = 'Farmer: Add new animal';

//labels
$lang['animal configuration'] = 'new animal configuration';
$lang['admin password'] = 'Password for the new admin';
$lang['animal administrator'] = 'animal administrator';
$lang['importUsers'] = 'import all users of the master wiki to the new animal';
$lang['currentAdmin'] = 'Set the current user as admin';
$lang['newAdmin'] = 'Create new admin user "admin"';
$lang['server configuration'] = 'server configuration';
$lang['htaccess setup'] = 'htaccess setup';
$lang['subdomain setup'] = 'Subdomain setup';
$lang['animal subdomain'] = 'animal subdomain';
$lang['bulkSingleSwitcher'] = 'Edit a single animal or all at once?';
$lang['bulkEdit'] = 'bulk edit all animals';
$lang['singleEdit'] = 'edit a single animal';
$lang['bulkEditForm'] = 'Activate or deactivate a plugin in all animals';
$lang['activate'] = 'Activate';
$lang['deactivate'] = 'Deactivate';
$lang['singleEditForm'] = 'edit the plugins of a specific animal';
$lang['submit'] = 'Submit';
$lang['reset'] = 'Reset';

// input placeholders
$lang['js']['animalSelect'] = 'Select an animal';
$lang['js']['pluginSelect'] = 'Select a plugin';

// error messages
$lang['animalname_missing'] = 'Please enter a name for the new animal.';
$lang['animalname_invalid'] = 'The animalname may only contain alphanumeric characters and hyphens(but not as first or last character).';
$lang['adminsetup_missing'] = 'Choose an admin for the new animal.';
$lang['adminPassword_empty'] = 'The password for the new admin account must not be empty.';
$lang['serversetup_missing'] = 'Choose either a subdomain setup and enter a valid subdomain or choose a htaccess setup.';
$lang['animalsubdomain_missing'] = 'Please enter a valid domain for the new animal.';
$lang['animalsubdomain_invalid'] = 'Please enter a valid domain without underscores.';

//Setup VIM: ex: et ts=4 :
