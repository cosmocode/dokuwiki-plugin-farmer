<?php
/**
 * German language file for farmer plugin
 *
 * @author Andreas Gohr <gohr@cosmocode.de>
 */

// menu entry for admin plugins
$lang['menu'] = 'Farming';

// tabs
$lang['tab_setup'] = 'Farm Setup';
$lang['tab_info'] = 'Info';
$lang['tab_config'] = 'Konfiguration';
$lang['tab_plugins'] = 'Plugins verwalten';
$lang['tab_new'] = 'Neues Animal hinzufügen';
$lang['tab_delete'] = 'Animal löschen';

// setup
$lang['preloadPHPForm'] = 'Farm aufsetzen';
$lang['farm dir'] = 'Animal-Verzeichnis';
$lang['htaccess setup'] = 'Farm code zu .htaccess hinzufügen?';
$lang['submit'] = 'Abschicken';
$lang['farmdir_missing'] = 'Bitte geben Sie das Verzeichnis an in dem die Animals gespeichert werden sollen.';
$lang['farmdir_in_dokuwiki'] = 'Das Animal-Verzeichnis (%s) muss außerhalb des Farm-DokuWikis (%s) liegen.';
$lang['farmdir_uncreatable'] = 'Das Animal-Verzeichnis (%s) konnte nicht erzeugt werden. Sind die Dateiberechtigungen korrekt?';
$lang['farmdir_unwritable'] = 'Bitte stellen Sie sicher, dass der Webserver in das Animal-Verzeichnis (%s) schreiben darf!';
$lang['farmdir_notEmpty'] = 'Das Animal-Verzeichnis (%s) muss leer sein!';
$lang['preload creation success'] = 'Die Farm wurde erfolgreich angelegt.';
$lang['preload creation error'] = 'Es ist ein Fehler beim Aufsetzen der Farm ausfgetreten.';
$lang['overwrite_preload'] = 'Achtung: Ihre existierende: inc/preload.php wird überschrieben, wenn Sie diesen hier weitermachen!';

// info
$lang['animal'] = 'Animal Name / Domain';
$lang['thisis'] = 'Diese Instanz ist';
$lang['thisis.farmer'] = 'Der Farmer!';
$lang['thisis.animal'] = 'Ein Animal!';
$lang['baseinstall'] = 'Farmer Installation';
$lang['animals'] = 'Animals';
$lang['confdir'] = 'Konfigurationsverzeichnis dieser Instanz';
$lang['savedir'] = 'data-Verzeichnis dieser Instanz';
$lang['plugins'] = 'In dieser Instanz aktivierte Plugins';

// config
$lang['base'] = 'Grundkonfiguration';
$lang['farm host'] = 'Farmer Host Name';
$lang['base domain'] = 'Basis-Domain für Subdomain-Animals';
$lang['conf_inherit'] = 'Farmer-Einstellungen die von Animals geerbt werden sollen';
$lang['conf_inherit_main'] = 'Konfigurationseinstellungen';
$lang['conf_inherit_acronyms'] = 'Abkürzungs-Definitionen';
$lang['conf_inherit_entities'] = 'Entity-Definitionionen';
$lang['conf_inherit_interwiki'] = 'Interwiki-Definitionen';
$lang['conf_inherit_license'] = 'Lizenz-Definitionen';
$lang['conf_inherit_mime'] = 'MIME-Type-Definitionen';
$lang['conf_inherit_scheme'] = 'URL-Scheme-Definitionen';
$lang['conf_inherit_smileys'] = 'Smiley-Definitionen';
$lang['conf_inherit_wordblock'] = 'Spamfiltereinträge';
$lang['conf_inherit_userstyle'] = 'Nutzer-Styles';
$lang['conf_inherit_userscript'] = 'Nutzer-Scripts';
$lang['conf_inherit_users'] = 'Benutzer (nur Plain Auth)';
$lang['conf_inherit_plugins'] = 'Plugin-Zustand';
$lang['conf_inherit_yes'] = 'vom Farmer geerbt';
$lang['conf_inherit_no'] = 'unabhängig vom Farmer';
$lang['conf_notfound'] = 'Verhalten bei zugriff auf nicht-existierende Animals';
$lang['conf_notfound_farmer'] = 'Zeige das Farmer-Wiki';
$lang['conf_notfound_404'] = 'Zeige eine 404-Fehlerseite';
$lang['conf_notfound_list'] = 'Zeige eine Liste der existierenden Animals';
$lang['conf_notfound_redirect'] = 'Leite auf untenstehende URL um';
$lang['conf_notfound_url'] = 'URL auf die umgeleitet werden soll wenn oben ausgewählt';
$lang['save'] = 'Speichern';

// new
$lang['animal template'] = 'Bestehendes Animal kopieren';
$lang['animal creation success'] = 'Das Animal "%s" wurde erfolgreich angelegt.';
$lang['animal creation error'] = 'Es gab einen Fehler beim Anlegen des Animals.';
$lang['animal configuration'] = 'Animal Grundkonfiguration';
$lang['animal administrator'] = 'Animal Administrator';
$lang['noUsers'] = 'Keine Benutzer erzeugen';
$lang['importUsers'] = 'Alle Benutzeraccounts des Farmers in das neue Animal kopieren';
$lang['currentAdmin'] = 'Den aktuellen Benutzer als Admin setzen';
$lang['newAdmin'] = 'Neuen Benutzer "admin" anlegen';
$lang['admin password'] = 'Passwort für den neuen Administrator';
$lang['animalname_missing'] = 'Bitte geben Sie einen Namen für das neue Animal an.';
$lang['animalname_invalid'] = ' Der Name des Animals darf nur aus Buchstaben und Ziffern sowie aus Bindestrichen und Punkten (nicht am Anfang oder Ende) bestehen.';
$lang['animalname_preexisting'] = 'Ein Animal mit diesem Namen existiert bereits.';
$lang['adminPassword_empty'] = 'Das Passwort für den neuen Administrator darf nicht leer sein.';
$lang['animal template copy error'] = 'Es gab ein Problem beim Kopieren von %s aus dem existierenden Animal in das neue.';

// plugins
$lang['bulkSingleSwitcher'] = 'Ein einzelnes Animal bearbeiten oder alle aufeinmal?';
$lang['bulkEdit'] = 'Alle Animals bearbeiten';
$lang['singleEdit'] = 'Ein einzelnes Animal bearbeiten';
$lang['bulkEditForm'] = 'Plugins in allen Animals ein- oder ausschalten';
$lang['activate'] = 'Aktivieren';
$lang['deactivate'] = 'Deaktivieren';
$lang['singleEditForm'] = 'Plugins eines spezifischen Animals bearbeiten';
$lang['plugindone'] = 'Plugin states updated';
$lang['plugin'] = 'Plugin';
$lang['plugin_on'] = 'an';
$lang['plugin_off'] = 'aus';
$lang['plugin_default'] = 'Voreinstellung';
$lang['plugin_enabled'] = 'Aktiviert';
$lang['plugin_disabled'] = 'Deaktiviert';
$lang['js']['animalSelect'] = 'Wählen Sie ein Animal';
$lang['js']['pluginSelect'] = 'Wählen Sie ein Plugin';

// delete
$lang['delete_animal'] = 'Animal zum Löschen auswählen';
$lang['delete_confirm'] = 'Name des Animals erneut eingeben, um Löschen zu bestätigen';
$lang['delete'] = 'Animal und alle darin gespeicherten Daten löschen';
$lang['delete_noanimal'] = 'Bitte wählen sie ein Animal zum Löschen aus';
$lang['delete_mismatch'] = 'Bestätigung stimmt nicht mit Animalnamen überein. Nicht gelöscht.';
$lang['delete_invalid'] = 'Invalider Animalname. Nicht gelöscht.';
$lang['delete_success'] = 'Animal erfolgreich gelöscht.';
$lang['delete_fail'] = 'Einige Dateien konnten nicht gelöscht werden. Sie sollten diese manuell entfernen.';

//Setup VIM: ex: et ts=4 :
