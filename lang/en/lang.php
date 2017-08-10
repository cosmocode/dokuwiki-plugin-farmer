<?php
/**
 * English language file for farmer plugin
 *
 * @author Michael Große <grosse@cosmocode.de>
 * @author Andreas Gohr <gohr@cosmocode.de>
 */

// menu entry for admin plugins
$lang['menu'] = 'Farming';

// tabs
$lang['tab_setup'] = 'Farm Setup';
$lang['tab_info'] = 'Info';
$lang['tab_config'] = 'Configuration';
$lang['tab_plugins'] = 'Manage Plugins';
$lang['tab_new'] = 'Add new Animal';
$lang['tab_delete'] = 'Delete Animal';

// setup
$lang['preloadPHPForm'] = 'Initialize Farming';
$lang['farm dir'] = 'Animal directory';
$lang['htaccess setup'] = 'Add farm code to .htaccess?';
$lang['submit'] = 'Submit';
$lang['farmdir_missing'] = 'Please enter a directory where the Animals should be stored.';
$lang['farmdir_in_dokuwiki'] = 'The Animal directory (%s) must be outside of the Farm dokuwiki (%s).';
$lang['farmdir_uncreatable'] = 'The Animal directory (%s) could not be created. Are the permissions correct?';
$lang['farmdir_unwritable'] = 'Please make sure that the webserver has write access in the Animal directory (%s)!';
$lang['farmdir_notEmpty'] = 'The Animal directory (%s) must be empty.';
$lang['preload creation success'] = 'Farming has been succesfully initialized.';
$lang['preload creation error'] = 'There was an error during Farming initialization.';
$lang['overwrite_preload'] = 'Warning: Your existing inc/preload.php will be overwritten when continuing here!';

// info
$lang['animal'] = 'Animal Name / Domain';
$lang['thisis'] = 'Instance is';
$lang['thisis.farmer'] = 'The farmer!';
$lang['thisis.animal'] = 'An Animal!';
$lang['baseinstall'] = 'Farmer Install';
$lang['animals'] = 'Animals';
$lang['confdir'] = 'Instance Configuration Directory';
$lang['savedir'] = 'Instance Data Directory';
$lang['plugins'] = 'Plugins active in this instance';

// config
$lang['base'] = 'Base Configuration';
$lang['farm host'] = 'Farmer Host Name';
$lang['base domain'] = 'Base Domain for subdomain Animals';
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
$lang['conf_inherit_users'] = 'Users (Plain Auth only)';
$lang['conf_inherit_plugins'] = 'Plugin State';
$lang['conf_inherit_yes'] = 'inherited from farmer';
$lang['conf_inherit_no'] = 'independent from farmer';
$lang['conf_notfound'] = 'Behavior on accessing nonexistent Animals';
$lang['conf_notfound_farmer'] = 'Show the farmer wiki';
$lang['conf_notfound_404'] = 'Show a 404 error page';
$lang['conf_notfound_list'] = 'Show a list of available animals';
$lang['conf_notfound_redirect'] = 'Redirect to the URL below';
$lang['conf_notfound_url'] = 'URL to redirect to if selected above';
$lang['save'] = 'Save';

// new
$lang['animal template'] = 'Copy existing Animal';
$lang['animal creation success'] = 'The Animal "%s" has been successfully created.';
$lang['animal creation error'] = 'There was an error while creating the Animal.';
$lang['animal configuration'] = 'Basic Animal configuration';
$lang['inherit user registration'] = 'Inherit user registration setting from farmer';
$lang['enable user registration'] = 'Allow users to register themselves';
$lang['disable user registration'] = 'Disable user register';
$lang['animal administrator'] = 'Animal administrator';
$lang['noUsers'] = 'Do not create any users';
$lang['importUsers'] = 'Import all users of the Farmer to the new Animal';
$lang['currentAdmin'] = 'Set the current user as admin';
$lang['newAdmin'] = 'Create new admin user "admin"';
$lang['admin password'] = 'Password for the new admin';
$lang['animalname_missing'] = 'Please enter a name for the new Animal.';
$lang['animalname_invalid'] = 'The Animal name may only contain alphanumeric characters and dots/hyphens (but not as first or last character).';
$lang['animalname_preexisting'] = 'An Animal with that name already exists.';
$lang['adminPassword_empty'] = 'The password for the new admin account must not be empty.';
$lang['animal template copy error'] = 'There was a problem copying %s from the existing Animal to the new one.';
$lang['aclpolicy missing/bad'] = 'Please choose an initial ACL policy from the dropdown.';

// plugins
$lang['bulkSingleSwitcher'] = 'Edit a single Animal or all at once?';
$lang['bulkEdit'] = 'Bulk edit all Animals';
$lang['singleEdit'] = 'Edit a single Animal';
$lang['bulkEditForm'] = 'Activate or deactivate a plugin in all Animals';
$lang['matrixEdit'] = 'Edit Animal/Plugin matrix';
$lang['default'] = 'Set to default';
$lang['activate'] = 'Activate';
$lang['deactivate'] = 'Deactivate';
$lang['singleEditForm'] = 'Edit the plugins of a specific Animal';
$lang['plugindone'] = 'Plugin states updated';
$lang['plugin'] = 'Plugin';
$lang['plugin_on'] = 'on';
$lang['plugin_off'] = 'off';
$lang['plugin_default'] = 'Default';
$lang['plugin_enabled'] = 'Enabled';
$lang['plugin_disabled'] = 'Disabled';
$lang['js']['animalSelect'] = 'Select an animal';
$lang['js']['pluginSelect'] = 'Select a plugin';
$lang['disable_new_plugins'] = 'The plugin has been disabled by default. You can enable it here or in specific animals only.';


// delete
$lang['delete_animal'] = 'Select Animal to delete';
$lang['delete_confirm'] = 'Please type the Animal name to confirm';
$lang['delete'] = 'Delete the Animal and all its data';

$lang['delete_noanimal'] = 'Please select an Animal to delete';
$lang['delete_mismatch'] = 'Confirmation does not match Animal name. Not deleted.';
$lang['delete_invalid'] = 'Invalid Animal name. Not deleted.';
$lang['delete_success'] = 'Animal successfully deleted.';
$lang['delete_fail'] = 'Some files could not be deleted, you should clean up manuallly.';

//Setup VIM: ex: et ts=4 :
