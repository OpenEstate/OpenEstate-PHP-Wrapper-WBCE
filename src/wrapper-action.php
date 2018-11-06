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

use \OpenEstate\PhpExport\Utils;
use function \htmlspecialchars as html;

// If this is defined we are on a FE page , just nice to know.
if (!defined('WB_FRONTEND')) define('WB_FRONTEND', true);

if (!defined('WB_DEBUG')) define('WB_DEBUG', false);
if (!defined('DEBUG')) define('DEBUG', WB_DEBUG);

// Include config file
$config_file = dirname(dirname(__DIR__)) . '/config.php';
if (!file_exists($config_file)) {
    echo 'Can\'t find configuration file at: ' . $config_file;
    return;
}
/** @noinspection PhpIncludeInspection */
require_once $config_file;

// Get section ID of the requested wrapper page
if (!isset($_REQUEST['section']) || !is_numeric($_REQUEST['section'])) {
    echo 'Invalid section was specified!';
    return;
}
$section_id = $_REQUEST['section'];

include('info.php');

/**
 * Database instance.
 *
 * var database $database
 */
global $database;

// load current settings
$settings = null;
$settingsResult = $database->query("SELECT `settings` FROM `$module_table` "
    . "WHERE `section_id` = $section_id "
    . "LIMIT 1");
if ($settingsResult->numRows() > 0) {
    $settingsRow = $settingsResult->fetchRow();
    $settings = unserialize(base64_decode($settingsRow['settings']));
} else {
    echo '<h3>' . html($module_i18n['error_no_settings']) . '</h3>';
    return;
}
if (!is_array($settings)) {
    $settings = array();
}

// append ID of current section to the settings
$settings['section_id'] = $section_id;

// init script environment
$scriptPath = isset($settings['env_path']) ? $settings['env_path'] : '';
if (strlen($scriptPath) > 0 && substr($scriptPath, -1) != '/') {
    $scriptPath .= '/';
}
$scriptUrl = isset($settings['env_url']) ? $settings['env_url'] : '';
if (strlen($scriptUrl) > 0 && substr($scriptUrl, -1) != '/') {
    $scriptUrl .= '/';
}
$errors = array();
$environment = openestate_wrapper_env(
    $scriptPath,
    $scriptUrl,
    true,
    $settings,
    $errors
);

// make sure, that the script environment was properly loaded
if ($environment === null || count($errors) > 0) {
    echo '<h3>' . html($module_i18n['error_invalid_settings']) . '</h3>';
    return;
}

try {
    // process the requested action, if necessary
    $actionResult = $environment->processAction();

    // send the result of the requested action
    ob_start();
    if ($actionResult === null) {
        \http_response_code(501);
        echo Utils::getJson(array('error' => 'No action was executed!'));
    } else {
        echo Utils::getJson($actionResult);
    }

} catch (\Exception $e) {

    //Utils::logError($e);
    Utils::logWarning($e);

    // ignore previously buffered output
    \ob_end_clean();
    \ob_start();

    if (!\headers_sent()) {
        \http_response_code(500);
    }
    echo Utils::getJson(array('error' => $e->getMessage()));
    exit(0);

} finally {

    $actionResult = ob_get_clean();
    $environment->shutdown();
    echo $actionResult;
    exit(0);

}