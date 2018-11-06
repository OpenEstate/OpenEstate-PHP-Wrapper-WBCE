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

/** @noinspection SqlNoDataSourceInspection */
/** @noinspection SqlDialectInspection */

// manually include the config.php file (defines the required constants)
/** @noinspection PhpIncludeInspection */
require(dirname(dirname(dirname(__FILE__))) . '/config.php');

// tell the admin wrapper to actualize the DB settings when this page was last updated
$update_when_modified = true;

// include the admin wrapper script (includes framework/class.admin.php)
require(WB_PATH . '/modules/admin.php');

if (!is_numeric($page_id) || !is_numeric($section_id)) {
    exit('Invalid request!');
}

include('info.php');

/**
 * Database instance.
 *
 * var database $database
 */
global $database;

/**
 * Administration messages.
 *
 * @var array $MESSAGE
 */
global $MESSAGE;

// load current settings
$settings = null;
$sectionResult = $database->query("SELECT `settings` FROM `$module_table` "
    . "WHERE `section_id` = $section_id "
    . "LIMIT 1");
if ($sectionResult->numRows() > 0) {
    $sectionRow = $sectionResult->fetchRow();
    $settings = unserialize(base64_decode($sectionRow['settings']));
} else {
    $settings = array();
}

// put changes into configuration of the current page
//load_default_immotool_settings($settings);
$settings['env_path'] = trim($admin->get_post('env_path'));
$settings['env_url'] = trim($admin->get_post('env_url'));
$settings['env_script'] = trim($admin->get_post('env_script'));
$settings['charset'] = trim($admin->get_post('charset'));
$settings['css'] = trim($admin->get_post('css'));
$settings['listingUrl'] = trim($admin->get_post('listingUrl'));
$settings['favUrl'] = trim($admin->get_post('favUrl'));
$settings['exposeUrl'] = trim($admin->get_post('exposeUrl'));

// save additional settings for the selected view
unset($settings['index']);
unset($settings['expose']);
unset($settings['fav']);
if ($settings['env_script'] === 'index') {
    $settings['index'] = $admin->get_post('index');
} else if ($settings['env_script'] === 'expose') {
    $settings['expose'] = $admin->get_post('expose');
} else if ($settings['env_script'] === 'fav') {
    $settings['fav'] = $admin->get_post('fav');
}

// save disabled components
$allComponents = explode(',', trim($admin->get_post('allComponents')));
$components = $admin->get_post('component');
if (!is_array($components)) $components = array();
$settings['disabledComponents'] = array();
foreach ($allComponents as $componentId) {
    if (!in_array($componentId, $components))
        $settings['disabledComponents'][] = $componentId;
}

// save enabled features components
$features = $admin->get_post('feature');
$settings['features'] = (is_array($features)) ?
    $features : array();

// make sure, that the path ends with a slash
$len = strlen($settings['env_path']);
if ($len > 0 && $settings['env_path'][$len - 1] != '/') {
    $settings['env_path'] .= '/';
}

// make sure, that the URL ends with a slash
$len = strlen($settings['env_url']);
if ($len > 0 && $settings['env_url'][$len - 1] != '/') {
    $settings['env_url'] .= '/';
}

//echo '<pre>' . print_r($settings, true) . '</pre>';

// save modified settings
$database->query("UPDATE `$module_table` "
    . "SET `settings` = '" . base64_encode(serialize($settings)) . "' "
    . "WHERE `section_id` = '$section_id'");
if ($database->is_error()) {
    $admin->print_error($database->get_error(), $js_back);
} else {
    $admin->print_success($MESSAGE['PAGES']['SAVED'], ADMIN_URL . '/pages/modify.php?page_id=' . $page_id);
}

// print admin footer
$admin->print_footer();
