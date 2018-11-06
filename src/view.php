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

if (!defined('WB_PATH')) {
    exit('Cannot access this file directly!');
}
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

//echo '<pre>' . print_r($settings, true) . '</pre>';
//echo '<pre>' . print_r($_SESSION, true) . '</pre>';
//die('STOP');

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

// make sure, that the script environment is not currently updated
if (is_file(Utils::joinPath($environment->getConfig()->basePath, 'immotool.php.lock'))) {
    echo '<h3>' . html($module_i18n['error_update_running']) . '</h3>';
    echo '<p>' . html($module_i18n['error_update_running_info']) . '</p>';
    return;
}

// determine the script to load
$wrap = (isset($_REQUEST['wrap'])) ? $_REQUEST['wrap'] : null;
if (!is_string($wrap) && isset($settings['env_script'])) {
    $wrap = $settings['env_script'];
}

ob_start();

try {
    // process the requested action, if necessary
    $actionResult = $environment->processAction();

    // wrap expose view
    if (strtolower($wrap) == 'expose') {

        $view = $environment->newExposeHtml();

        $exposeSettings = (isset($settings['expose']) && is_array($settings['expose'])) ?
            $settings['expose'] : array();

        if (isset($exposeSettings['lang'])) {
            $lang = (isset($exposeSettings['lang'])) ? strtolower(trim($exposeSettings['lang'])) : null;
            if ($lang != null && $environment->isSupportedLanguage($lang)) {
                $environment->setLanguage($lang);
            }
        }

        if ($view->getObjectId() == null) {
            $view->setObjectId(isset($exposeSettings['id']) ? $exposeSettings['id'] : null);
        }

    } // wrap favorite view
    else if (strtolower($wrap) == 'fav') {

        $view = $environment->newFavoriteHtml();

        $favSettings = (isset($settings['fav']) && is_array($settings['fav'])) ?
            $settings['fav'] : array();

        if (isset($favSettings['lang'])) {
            $lang = (isset($favSettings['lang'])) ? strtolower(trim($favSettings['lang'])) : null;
            if ($lang != null && $environment->isSupportedLanguage($lang)) {
                $environment->setLanguage($lang);
            }
        }

        if (!isset($_REQUEST['wrap']) && !isset($_REQUEST['update'])) {
            $environment->getSession()->setFavoritePage(null);
            $environment->getSession()->setFavoriteView(
                (isset($favSettings['view'])) ? $favSettings['view'] : null);
            $environment->getSession()->setFavoriteOrder(
                (isset($favSettings['order_by'])) ? $favSettings['order_by'] : null);
            $environment->getSession()->setFavoriteOrderDirection(
                (isset($favSettings['order_dir'])) ? $favSettings['order_dir'] : null);
        }

    } // wrap listing view by default
    else {

        $view = $environment->newListingHtml();

        $indexSettings = (isset($settings['index']) && is_array($settings['index'])) ?
            $settings['index'] : array();

        if (isset($indexSettings['lang'])) {
            $lang = (isset($indexSettings['lang'])) ? strtolower(trim($indexSettings['lang'])) : null;
            if ($lang != null && $environment->isSupportedLanguage($lang)) {
                $environment->setLanguage($lang);
            }
        }

        if (!isset($_REQUEST['wrap']) && !isset($_REQUEST['update'])) {
            $environment->getSession()->setListingPage(null);
            $environment->getSession()->setListingView(
                (isset($indexSettings['view'])) ? $indexSettings['view'] : null);
            $environment->getSession()->setListingFilters(
                (isset($indexSettings['filter'])) ? $indexSettings['filter'] : null);
            $environment->getSession()->setListingOrder(
                (isset($indexSettings['order_by'])) ? $indexSettings['order_by'] : null);
            $environment->getSession()->setListingOrderDirection(
                (isset($indexSettings['order_dir'])) ? $indexSettings['order_dir'] : null);
        }
    }

    // generate content
    echo $view->process();

    // writer header elements before content
    foreach ($view->getHeaders() as $header) {
        if ($header instanceof \OpenEstate\PhpExport\Html\Javascript) {
            echo "\n<!--(MOVE) JS HEAD BTM- -->\n" . $header->generate() . "\n<!--(END)-->";
        } else if ($header instanceof \OpenEstate\PhpExport\Html\Stylesheet) {
            echo "\n<!--(MOVE) CSS HEAD BTM- -->\n" . $header->generate() . "\n<!--(END)-->";
        } else if ($header instanceof \OpenEstate\PhpExport\Html\Meta) {
            if ($header->name == 'description') {
                echo "\n<!--(REPLACE) META DESC -->\n" . $header->generate() . "\n<!--(END)-->";
            } else if ($header->name == 'keywords') {
                echo "\n<!--(REPLACE) META KEY -->\n" . $header->generate() . "\n<!--(END)-->";
            }
        }
    }

    // write custom css
    $customCss = (isset($settings['css'])) ? trim($settings['css']) : '';
    if ($customCss !== '') {
        echo "\n" . '<!--(MOVE) CSS HEAD BTM- -->';
        echo "\n" . '<style type="text/css">';
        echo "\n" . html($customCss);
        echo "\n" . '</style>';
        echo "\n" . '<!--(END)-->';
    }

    // change page title for expose views
    if ($view instanceof \OpenEstate\PhpExport\View\ExposeHtml) {
        echo "\n<!--(REPLACE) TITLE -->\n<title>" . html($view->getTitle()) . "</title>\n<!--(END)-->";
    }

} catch (\Exception $e) {

    //Utils::logError($e);
    Utils::logWarning($e);

    // ignore previously buffered output
    ob_end_clean();

    echo '<h3>' . html($module_i18n['error_internal']) . '</h3>'
        . '<p>' . html($e->getMessage()) . '</p>'
        . '<pre>' . html($e) . '</pre>';

} finally {

    $content = ob_get_clean();
    $environment->shutdown();
    echo $content;

}
