<?php
/**
 * English language file for farmer plugin
 *
 * @author Michael Große <grosse@cosmocode.de>
 */

// menu entry for admin plugins
$lang['menu'] = 'Farming';

$lang['subdomain_helptext_injection'] = 'If you are using the subdomain based approach you need to enter the subdomain under which the new subwiki will be reachable';

// tabs
$lang['tab_info'] = 'Info';
$lang['tab_setup'] = 'Farm Setup';
$lang['tab_config'] = 'Configuration';
$lang['tab_plugins'] = 'Manage Plugins';
$lang['tab_new'] = 'Add new Animal';

//labels
$lang['preloadPHPForm'] = 'Create a new preload.php';
$lang['base'] = 'Base Configuration';
$lang['farm dir'] = 'Animal Directory';
$lang['farm host'] = 'Farmer Host Name';
$lang['base domain'] = 'Base Domain for subdomain animals';
$lang['animal configuration'] = 'Basic animal configuration';
$lang['admin password'] = 'Password for the new admin';
$lang['animal administrator'] = 'Animal administrator';
$lang['importUsers'] = 'Import all users of the master wiki to the new animal';
$lang['currentAdmin'] = 'Set the current user as admin';
$lang['newAdmin'] = 'Create new admin user "admin"';
$lang['htaccess setup'] = 'Add farm code to .htaccess?';
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

$lang['animal name'] = 'Animal Name / Domain';

$lang['htaccess_basedir'] = 'Enter the path to the above-entered farm directory relativ to the server root:';

$lang['animal'] = 'Animal Name';
$lang['thisis'] = 'Instance is';
$lang['thisis.farmer'] = 'The farmer!';
$lang['thisis.animal'] = 'An animal!';
$lang['baseinstall'] = 'Farmer Install';
$lang['animals'] = 'Animals';
$lang['confdir'] = 'Instance Configuration Directory';
$lang['savedir'] = 'Instance Data Directory';

// config
$lang['conf_inherit'] = 'Farmer Settings Animals should inherit';
$lang['conf_inherit_main'] = 'Configuration Settings';
$lang['conf_inherit_acronyms'] = 'Acronym Definitions';
$lang['conf_inherit_entities'] = 'Entity Definitions';
$lang['conf_inherit_interwiki'] = 'Interwiki Definitions';
$lang['conf_inherit_license'] = 'License Definitions';
$lang['conf_inherit_mime'] = 'MIME Type Definitions';
$lang['conf_inherit_scheme'] = 'URL Scheme Definitions';
$lang['conf_inherit_smileys'] = 'Smiley Definitions';
$lang['conf_inherit_wordblock'] = 'Spam Blacklist Entries';
$lang['conf_inherit_userstyle'] = 'User Styles';
$lang['conf_inherit_userscript'] = 'User Scripts';
$lang['conf_inherit_yes'] = 'inherited from farmer';
$lang['conf_inherit_no'] = 'independent from farmer';

$lang['conf_notfound'] = 'Behavior on accessing nonexistent Animals';
$lang['conf_notfound_farmer'] = 'Show the farmer wiki';
$lang['conf_notfound_404'] = 'Show a 404 error page';
$lang['conf_notfound_list'] = 'Show a list of available animals';
$lang['conf_notfound_redirect'] = 'Redirect to the URL below';
$lang['conf_notfound_url'] = 'URL to redirect to if selected above';

$lang['save'] = 'Save';

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
$lang['overwrite_preload'] = 'Warning: Your existing inc/preload.php will be overwritten when continuing here!';

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

