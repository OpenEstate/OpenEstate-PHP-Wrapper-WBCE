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
$i18n['description'] = 'This module integrates real estates from OpenEstate-ImmoTool into your WBCE website.';
$i18n['setup'] = 'Configure exported scripts';
$i18n['view'] = 'Configure generated view';

// Informationen
$i18n['info_module'] = 'Module';
$i18n['info_version'] = 'Version';
$i18n['info_license'] = 'License';
$i18n['info_authors'] = 'Authors';
$i18n['info_support_us'] = 'Support us!';

// Anbindung
$i18n['setup_validate'] = 'validation';
$i18n['setup_success'] = 'The exported scripts are correctly configured!';
$i18n['setup_problem'] = 'The exported scripts are NOT correctly configured!';
$i18n['setup_errors'] = 'error messages';
$i18n['setup_step_export'] = 'Export your properties from ImmoTool to your website via PHP.';
$i18n['setup_step_config'] = 'Configure path and URL, that points to the exported scripts, and click \'Save\' to perform a new validation.';
$i18n['setup_path'] = 'script path';
$i18n['setup_path_info'] = 'Enter the path on your server, that points to the exported scripts. The path of this CMS installation is:';
$i18n['setup_url'] = 'script URL';
$i18n['setup_url_info'] = 'Enter the URL on your server, that points to the exported scripts. The URL of this CMS installation is:';

// Immobilienübersicht
$i18n['view_index'] = 'Property listing';
$i18n['view_index_view'] = 'view';
$i18n['view_index_view_detail'] = 'as table';
$i18n['view_index_view_thumb'] = 'as thumbnails';
$i18n['view_index_language'] = 'language';
$i18n['view_index_order'] = 'order';
$i18n['view_index_order_asc'] = 'ascending';
$i18n['view_index_order_desc'] = 'descending';
$i18n['view_index_filter'] = 'filter by %s';

// Exposéansicht
$i18n['view_expose'] = 'Property details';
$i18n['view_expose_id'] = 'property ID';
$i18n['view_expose_language'] = 'language';

// Vormerkliste
$i18n['view_fav'] = 'Favorites';
$i18n['view_fav_view'] = 'view';
$i18n['view_fav_view_detail'] = 'as table';
$i18n['view_fav_view_thumb'] = 'as thumbnails';
$i18n['view_fav_language'] = 'language';
$i18n['view_fav_order'] = 'order';
$i18n['view_fav_order_asc'] = 'ascending';
$i18n['view_fav_order_desc'] = 'descending';

// Optionen
$i18n['options'] = 'Further options';
$i18n['options_charset'] = 'charset';
$i18n['options_charset_info'] = 'Enter the charset, that is used on this website.';
$i18n['options_css'] = 'stylesheet';
$i18n['options_css_info'] = 'You can provide custom stylesheets, that are loaded together with the PHP export.';
$i18n['options_components'] = 'components';
$i18n['options_components_info'] = 'The PHP export integrates these third party components into your website. If your website already uses some of these components, you can disable them accordingly.';
$i18n['options_features'] = 'features';
$i18n['options_features_filtering'] = 'Enable filtering of object listings.';
$i18n['options_features_ordering'] = 'Enable ordering of object listings.';
$i18n['options_features_favorites'] = 'Enable favorites.';
$i18n['options_features_languages'] = 'Enable language selection.';
$i18n['options_listingUrl'] = 'listings URL';
$i18n['options_listingUrl_info'] = 'You may enter an URL, that is used to link object listings from this page. If no URL is provided, listings are shown on the current page.';
$i18n['options_favUrl'] = 'favorites URL';
$i18n['options_favUrl_info'] = 'You may enter an URL, that is used to link favorite listings from this page. If no URL is provided, favorites are shown on the current page.';
$i18n['options_exposeUrl'] = 'object URL';
$i18n['options_exposeUrl_info'] = 'You may enter an URL, that is used to link single objects from this page. If no URL is provided, single objects are shown on the current page.';

// Fehler
$i18n['error_no_settings'] = 'Can\t find settings for this page!';
$i18n['error_invalid_settings'] = 'The settings for this page are invalid!';
$i18n['error_update_running'] = 'The properties are currently updated!';
$i18n['error_update_running_info'] = 'Please revisit this page after some minutes.';
$i18n['error_internal'] = 'An internal error occurred!';
$i18n['error_no_export_path'] = 'Please enter a valid script path!';
$i18n['error_invalid_export_path'] = 'It seems, that there is no PHP export available within the script path.';
$i18n['error_old_version'] = 'It seems, that you\'re using an unsupported version of PHP export.';
$i18n['error_unknown_version'] = 'Can\'t detect the script version!';
$i18n['error_init'] = 'Can\'t init script environment!';

return $i18n;
