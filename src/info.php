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

use \OpenEstate\PhpExport\Environment;
use \OpenEstate\PhpExport\WrapperConfig;

/**
 * Internal module name.
 *
 * @var string
 */
$module_directory = 'openestate_php_wrapper';

/**
 * Visible module name.
 *
 * @var string
 */
$module_name = 'OpenEstate PHP-Wrapper';

/**
 * Module type.
 *
 * @var string
 */
$module_function = 'page';

/**
 * Module version.
 *
 * @var string
 */
$module_version = '0.1-SNAPSHOT';

/**
 * Supported WBCE version.
 *
 * @var string
 */
$module_platform = '1.3.x';

/**
 * Module author.
 *
 * @var string
 */
$module_author = 'Andreas Rudolph, Walter Wagner';

/**
 * Module license.
 *
 * @var string
 */
$module_license = 'GNU General Public License 2 (or later)';

/**
 * Module description.
 *
 * @var string
 */
$module_description = 'This module integrates real estates from OpenEstate-ImmoTool into your WBCE website.';

/**
 * Name of the database table, where module settings are stored.
 *
 * @var string
 */
$module_table = TABLE_PREFIX . 'mod_' . $module_directory;

/**
 * Module translations.
 *
 * @var array
 */
$module_i18n = null;
if (!defined('DEFAULT_LANGUAGE')) {
    define('DEFAULT_LANGUAGE', 'EN');
}
foreach (array(LANGUAGE, DEFAULT_LANGUAGE, 'EN') as $lang) {
    $i18n_file = WB_PATH . '/modules/' . $module_directory . '/lang/' . $lang . '.php';
    if (!is_file($i18n_file)) {
        continue;
    }
    /** @noinspection PhpIncludeInspection */
    $module_i18n = include($i18n_file);
    if (is_array($module_i18n)) {
        break;
    }
}
if (!is_array($module_i18n)) {
    echo 'Can\'t load module translation!<hr/>';
    $module_i18n = array();
} else {
    // load translated module description
    $module_description = $module_i18n['description'];
}

if (!function_exists('openestate_wrapper_env')) {
    /**
     * Init script environment.
     *
     * @param string $scriptPath
     * Path, that contains to the script environment.
     *
     * @param string $scriptUrl
     * URL, that points to the script environment.
     *
     * @param boolean $initSession
     * Initialize the user session.
     *
     * @param array $settings
     * Associative array, that holds wrapper settings.
     *
     * @param array $errors
     * Errors during initialization.
     *
     * @return Environment
     * The initialized environment or null, if initialization failed.
     */
    function openestate_wrapper_env($scriptPath, $scriptUrl, $initSession, $settings, &$errors)
    {
        global $module_i18n;

        if (!is_dir($scriptPath)) {
            $errors[] = $module_i18n['error_no_export_path'];
            return null;
        }

        if (is_file($scriptPath . 'include/functions.php')) {
            if (!defined('IN_WEBSITE')) {
                define('IN_WEBSITE', 1);
            }
            if (!defined('IMMOTOOL_BASE_PATH')) {
                define('IMMOTOOL_BASE_PATH', $scriptPath);
            }

            /** @noinspection PhpIncludeInspection */
            require_once($scriptPath . 'include/functions.php');

            $oldVersionNumber = (defined('IMMOTOOL_SCRIPT_VERSION')) ? IMMOTOOL_SCRIPT_VERSION : '???';
            $errors[] = $module_i18n['error_old_version'] . ' (' . $oldVersionNumber . ')';
        } else if (!is_file($scriptPath . 'index.php') ||
            !is_file($scriptPath . 'expose.php') ||
            !is_file($scriptPath . 'fav.php') ||
            !is_file($scriptPath . 'config.php') ||
            !is_dir($scriptPath . 'include') ||
            !is_dir($scriptPath . 'include/OpenEstate') ||
            !is_file($scriptPath . 'include/init.php')
        ) {
            $errors[] = $module_i18n['error_invalid_export_path'];
        }
        if (count($errors) > 0) {
            return null;
        }

        /** @noinspection PhpIncludeInspection */
        require_once($scriptPath . 'include/init.php');

        /** @noinspection PhpIncludeInspection */
        require_once($scriptPath . 'config.php');

        if (!defined('OpenEstate\PhpExport\VERSION')) {
            $errors[] = $module_i18n['error_unknown_version'];
            return null;
        }

        require_once(__DIR__ . '/wrapper-config.php');

        try {
            $config = new WrapperConfig($scriptPath, $scriptUrl, $settings);
            //echo '<pre>' . print_r( $config, true ) . '</pre>';
            return new Environment($config, $initSession);
        } catch (\Exception $e) {
            $errors[] = $module_i18n['error_init'] . ' ' . $e->getMessage();;
            return null;
        }
    }
}
