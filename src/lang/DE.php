<?php
/*
 * A WBCE module for the OpenEstate-PHP-Export
 * Copyright (C) 2010-2018 OpenEstate.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

$i18n = array();

// Allgemein
$i18n['description'] = 'Dieses Modul integriert Immobilien aus OpenEstate-ImmoTool in Ihre WBCE-Webseite.';
$i18n['setup'] = 'Anbindung der exportierten PHP-Skripte';
$i18n['view'] = 'Darstellung auf der Seite';

// Informationen
$i18n['info_module'] = 'Modul';
$i18n['info_version'] = 'Version';
$i18n['info_license'] = 'Lizenz';
$i18n['info_authors'] = 'Autoren';
$i18n['info_support_us'] = 'Unterstützung';

// Anbindung
$i18n['setup_validate'] = 'Überprüfung';
$i18n['setup_success'] = 'Die ImmoTool-Skripte sind korrekt eingebunden!';
$i18n['setup_problem'] = 'Die ImmoTool-Skripte sind NICHT korrekt eingebunden!';
$i18n['setup_errors'] = 'Fehlermeldungen';
$i18n['setup_step_export'] = 'Führen Sie einen PHP-Export via ImmoTool auf diesen Webspace durch.';
$i18n['setup_step_config'] = 'Tragen Sie den Pfad und URL des Exportes ein und klicken Sie zur erneuten Prüfung auf \'Speichern\'.';
$i18n['setup_path'] = 'Skript-Pfad';
$i18n['setup_path_info'] = 'Tragen Sie hier den Pfad des Servers ein, wo die vom ImmoTool erzeugten Skripte abgelegt wurden. Der Serverpfad dieser CMS-Installation lautet:';
$i18n['setup_url'] = 'Skript-URL';
$i18n['setup_url_info'] = 'Tragen Sie hier die Webadresse ein, über welche der ImmoTool-Export aus dem Internet erreichbar ist. Die Webadresse dieser CMS-Installation lautet:';

// Immobilienübersicht
$i18n['view_index'] = 'Immobilienübersicht';
$i18n['view_index_view'] = 'Ansicht';
$i18n['view_index_view_detail'] = 'als Liste';
$i18n['view_index_view_thumb'] = 'als Galerie';
$i18n['view_index_language'] = 'Sprache';
$i18n['view_index_order'] = 'Sortierung';
$i18n['view_index_order_asc'] = 'aufsteigend';
$i18n['view_index_order_desc'] = 'absteigend';
$i18n['view_index_filter'] = 'nach %s filtern';

// Exposéansicht
$i18n['view_expose'] = 'Exposéansicht';
$i18n['view_expose_id'] = 'ID der Immobilie';
$i18n['view_expose_language'] = 'Sprache';

// Vormerkliste
$i18n['view_fav'] = 'Vormerkliste';
$i18n['view_fav_view'] = 'Ansicht';
$i18n['view_fav_view_detail'] = 'als Liste';
$i18n['view_fav_view_thumb'] = 'als Galerie';
$i18n['view_fav_language'] = 'Sprache';
$i18n['view_fav_order'] = 'Sortierung';
$i18n['view_fav_order_asc'] = 'aufsteigend';
$i18n['view_fav_order_desc'] = 'absteigend';

// Optionen
$i18n['options'] = 'Weitere Optionen';
$i18n['options_charset'] = 'Zeichensatz';
$i18n['options_charset_info'] = 'Enter the charset, that is used on this website.';
$i18n['options_css'] = 'Stylesheet';
$i18n['options_css_info'] = 'Bei Bedarf können Stylesheets hinterlegt werden, die bei der Einbindung zusätzlich geladen werden.';
$i18n['options_components'] = 'Komponenten';
$i18n['options_components_info'] = 'Der PHP-Export integriert folgende Komponenten von Drittanbietern in die Webseite. Wenn einzelne Komponenten bereits auf Ihrer Webseite genutzt werden, können diese hier deaktiviert werden.';
$i18n['options_features'] = 'Funktionen';
$i18n['options_features_filtering'] = 'Filterung der Immobilienübersicht aktivieren.';
$i18n['options_features_ordering'] = 'Sortierung von Immobilienlisten aktivieren.';
$i18n['options_features_favorites'] = 'Vormerkliste aktivieren.';
$i18n['options_features_languages'] = 'Auswahl der Sprache aktivieren.';
$i18n['options_listingUrl'] = 'Angebots-URL';
$i18n['options_listingUrl_info'] = 'Bei Bedarf kann eine URL eingetragen werden um von dieser Seite auf Angebotslisten zu verlinken. Wenn keine URL angegeben wurde, werden Angebotslisten auf der aktuellen Seite dargestellt.';
$i18n['options_favUrl'] = 'Favoriten-URL';
$i18n['options_favUrl_info'] = 'Bei Bedarf kann eine URL eingetragen werden um von dieser Seite auf die Vormerkliste zu verlinken. Wenn keine URL angegeben wurde, wird die Vormerkliste auf der aktuellen Seite dargestellt.';
$i18n['options_exposeUrl'] = 'Exposé-URL';
$i18n['options_exposeUrl_info'] = 'Bei Bedarf kann eine URL eingetragen werden um von dieser Seite auf Einzelansichten einer Immobilie zu verlinken. Wenn keine URL angegeben wurde, werden Einzelansichten auf der aktuellen Seite dargestellt.';

// Fehler
$i18n['error_no_settings'] = 'Keine Einstellungen zu dieser Seite gefunden!';
$i18n['error_invalid_settings'] = 'Die Einstellungen zu dieser Seite sind ungültig!';
$i18n['error_update_running'] = 'Der Immobilienbestand wird momentan aktualisiert!';
$i18n['error_update_running_info'] = 'Bitte besuchen Sie diese Seite in wenigen Minuten erneut.';
$i18n['error_internal'] = 'Ein interner Fehler ist aufgetreten!';
$i18n['error_no_export_path'] = 'Bitte tragen Sie einen gültigen Export-Pfad ein!';
$i18n['error_invalid_export_path'] = 'Es ist anscheinend kein PHP-Export unter dem angegebenen Pfad vorhanden!';
$i18n['error_old_version'] = 'Sie verwenden anscheinend eine nicht unterstützte Version des PHP-Exports.';
$i18n['error_unknown_version'] = 'Die Version des PHP-Exports kann nicht ermittelt werden!';
$i18n['error_init'] = 'Der PHP-Export kann nicht initialisiert werden!';

return $i18n;
