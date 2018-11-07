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
use const \OpenEstate\PhpExport\VERSION;
use function \htmlspecialchars as html;

if (!defined('WB_PATH')) {
    exit('Cannot access this file directly!');
}
if (!is_numeric($section_id) || !is_numeric($page_id)) {
    exit('Invalid request!');
}
if (!defined('WB_URL')) {
    define('WB_URL', dirname(dirname(dirname($_SERVER['REQUEST_URI']))));
}

/**
 * Database instance.
 *
 * var database $database
 */
global $database;

/**
 * Administration texts.
 *
 * @var array $TEXT
 */
global $TEXT;

include('info.php');

// load page infos from the database
$pageResult = $database->query("SELECT * FROM `" . TABLE_PREFIX . "pages` "
    . "WHERE page_id = $page_id "
    . "LIMIT 1");
if ($pageResult->numRows() == 0) {
    echo $module_i18n['error_no_settings'];
    return;
}
$pageRow = $pageResult->fetchRow();
$pageLink = $pageRow['link'];

// load wrapper settings from the database
$settingsResult = $database->query("SELECT `settings` FROM `$module_table` "
    . "WHERE `section_id` = $section_id "
    . "LIMIT 1");
if ($settingsResult->numRows() == 0) {
    echo $module_i18n['error_no_settings'];
    return;
}
$settingsRow = $settingsResult->fetchRow();
$settings = unserialize(base64_decode($settingsRow['settings']));
if (!is_array($settings)) {
    $settings = array();
}

// append ID of current section to the settings
$settings['section_id'] = $section_id;

// init script environment
$errors = array();
$envPath = (isset($settings['env_path'])) ? $settings['env_path'] : '';
$envUrl = (isset($settings['env_url'])) ? $settings['env_url'] : '';
$envScript = (isset($settings['env_script'])) ? $settings['env_script'] : null;
if ($envScript == null || $envScript == '') $envScript = 'index';
$environment = openestate_wrapper_env($envPath, $envUrl, false, $settings, $errors);

// set current language, if available
if ($environment !== null) {
    $lang = defined('DEFAULT_LANGUAGE') ? strtolower(DEFAULT_LANGUAGE) : 'en';
    if ($environment->isSupportedLanguage($lang)) {
        $environment->setLanguage($lang);
    } else {
        Utils::createTranslator($environment)->register();
    }
}
?>

<style type="text/css">
    table.openestate-wrapper-admin {
        border: none;
        width: 100%;
    }

    table.openestate-wrapper-admin td {
        border: none;
    }

    table.openestate-wrapper-admin td:first-child {
        vertical-align: top;
        width: 20%;
        text-align: right;
        white-space: nowrap;
        padding-right: 1em;
    }

    table.openestate-wrapper-admin td:not(first-child) {
        padding-bottom: 0.8em;
    }

    table.openestate-wrapper-admin label {
        margin-right: 0;
    }

    table.openestate-wrapper-admin q {
        padding-left: 3px;
        padding-right: 3px;
    }

    table.openestate-wrapper-admin input[type=text],
    table.openestate-wrapper-admin textarea,
    table.openestate-wrapper-admin select {
        width: 100%;
    }

    table.openestate-wrapper-admin textarea {
        height: 8em;
    }
</style>

<script type="text/javascript">
    <!--
    function show_wrapper_settings($value) {
        document.getElementById('openestate_index_settings').style.visibility = ($value === 'index') ? 'visible' : 'collapse';
        document.getElementById('openestate_expose_settings').style.visibility = ($value === 'expose') ? 'visible' : 'collapse';
        document.getElementById('openestate_fav_settings').style.visibility = ($value === 'fav') ? 'visible' : 'collapse';
    }

    //-->
</script>

<div style="margin-top:1em;">


    <div style="float:right; width:175px;">
        <h2 style="margin-top:0;"><?= html($module_i18n['info_module']) ?></h2>
        <div style="text-align:center;">
            <?= html($module_name) ?><br>
            <?= html($module_version) ?>
        </div>
        <h2><?= html($module_i18n['info_license']) ?></h2>
        <div style="text-align:center;">
            <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank"><?= html($module_license) ?></a>
        </div>
        <h2><?= html($module_i18n['info_authors']) ?></h2>
        <div style="text-align:center;">
            <a href="https://openestate.org/" target="_blank">
                <img src="<?= WB_URL ?>/modules/<?= $module_directory ?>/openestate.png" border="0" alt=""/>
                <div style="margin-top:0.5em;"><?= html($module_author) ?></div>
            </a>
        </div>
        <h2><?= html($module_i18n['info_support_us']) ?></h2>
        <div style="text-align:center;">
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="11005790">
                <input type="image" src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" name="submit"
                       alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
                <img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
            </form>
        </div>
    </div>

    <form name="edit" action="<?= WB_URL ?>/modules/<?= $module_directory ?>/save.php" method="post"
          style="margin-right:220px;">

        <input type="hidden" name="page_id" value="<?= html($page_id) ?>">
        <input type="hidden" name="section_id" value="<?= html($section_id) ?>">

        <h2 style="margin-top:0;"><?= html($module_i18n['setup']) ?></h2>
        <table class="openestate-wrapper-admin">
            <tr>
                <td><?= html($module_i18n['setup_validate']) ?></td>
                <td>
                    <?php
                    if ($environment !== null) {
                        echo '<strong style="color:green;">' .
                            html($module_i18n['setup_success']) . '<br>' .
                            '<span style="font-size:0.7em;">' . html($module_i18n['info_version']) . ' ' . VERSION . '</span>' .
                            '</strong>';
                    } else {
                        echo '<strong style="color:red;">' . html($module_i18n['setup_problem']) . '</strong><br>'
                            . '<ul>' .
                            '<li style="color:red;">&raquo; ' . html($module_i18n['setup_step_export']) . '</li>' .
                            '<li style="color:red;">&raquo; ' . html($module_i18n['setup_step_config']) . '</li>' .
                            '</ul>' .
                            '<strong style="color:red;">' . html($module_i18n['setup_errors']) . '</strong>' .
                            '<ul>';
                        foreach ($errors as $error) {
                            echo '<li style="color:red;">&raquo; ' . html($error) . '</li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="openestate_env_path"><?= html($module_i18n['setup_path']) ?></label>
                </td>
                <td>
                    <input id="openestate_env_path" name="env_path" type="text" value="<?= html($envPath) ?>"><br>
                    <em><?= html($module_i18n['setup_path_info']) ?></em><br>
                    <strong><?= html(WB_PATH) ?></strong>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="openestate_env_url"><?= html($module_i18n['setup_url']) ?></label>
                </td>
                <td>
                    <input id="openestate_env_url" name="env_url" type="text" value="<?= html($envUrl) ?>"><br>
                    <em><?= html($module_i18n['setup_url_info']) ?></em><br>
                    <strong><?= html(WB_URL) ?></strong>
                </td>
            </tr>
        </table>
        <?php if ($environment !== null) {

            // load index settings
            $indexSettings = (isset($settings['index']) && is_array($settings['index'])) ?
                $settings['index'] : array();
            if (!isset($indexSettings['view']))
                $indexSettings['view'] = 'detail';
            if (!isset($indexSettings['order_by']))
                $indexSettings['order_by'] = 'ObjectId';
            if (!isset($indexSettings['order_dir']))
                $indexSettings['order_dir'] = 'desc';

            // load expose settings
            $exposeSettings = (isset($settings['expose']) && is_array($settings['expose'])) ?
                $settings['expose'] : array();
            if (!isset($exposeSettings['id']))
                $exposeSettings['id'] = '';

            // load favorite settings
            $favSettings = (isset($settings['fav']) && is_array($settings['fav'])) ?
                $settings['fav'] : array();
            if (!isset($favSettings['view']))
                $favSettings['view'] = 'detail';
            if (!isset($favSettings['order_by']))
                $favSettings['order_by'] = 'ObjectId';
            if (!isset($favSettings['order_dir']))
                $favSettings['order_dir'] = 'desc';

            // load further settings
            if (!isset($settings['language']) || $settings['language'] == '')
                $settings['language'] = $environment->getLanguage();
            if (!isset($settings['charset']) || $settings['charset'] == '')
                $settings['charset'] = $environment->getConfig()->charset;
            if (!isset($settings['css']))
                $settings['css'] = '';
            if (!isset($settings['disabledComponents']) || !is_array($settings['disabledComponents']))
                $settings['disabledComponents'] = array();
            if (!isset($settings['features']) || !is_array($settings['features']))
                $settings['features'] = array();
            if (!isset($settings['listingUrl']))
                $settings['listingUrl'] = '';
            if (!isset($settings['favUrl']))
                $settings['favUrl'] = '';
            if (!isset($settings['exposeUrl']))
                $settings['exposeUrl'] = '';

            ?>
            <h2><?= html($module_i18n['view']) ?></h2>
            <h3>
                <label>
                    <input type="radio" name="env_script" value="index"
                           onchange="show_wrapper_settings('index');" <?= ($envScript === 'index') ? 'checked="checked"' : '' ?>/>
                    <?= html($module_i18n['view_index']) ?> / index.php
                </label>
            </h3>
            <table id="openestate_index_settings" class="openestate-wrapper-admin"
                   style="visibility:<?= ($envScript === 'index') ? 'visible' : 'collapse' ?>;">

                <tr>
                    <td>
                        <label for="openestate_index_view"><?= html($module_i18n['view_index_view']) ?></label>
                    </td>
                    <td>
                        <select id="openestate_index_view" name="index[view]">
                            <option value="detail" <?= ($indexSettings['view'] == 'detail') ? 'selected="selected"' : '' ?>>
                                <?= html($module_i18n['view_index_view_detail']) ?>
                            </option>
                            <option value="thumb" <?= ($indexSettings['view'] == 'thumb') ? 'selected="selected"' : '' ?>>
                                <?= html($module_i18n['view_index_view_thumb']) ?>
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="openestate_index_order_by"><?= html($module_i18n['view_index_order']) ?></label>
                    </td>
                    <td>
                        <select id="openestate_index_order_by" name="index[order_by]">
                            <?php
                            $orders = array();
                            $titles = array();
                            foreach ($environment->getConfig()->getOrderObjects() as $orderObj) {
                                /**
                                 * order instance
                                 * @var \OpenEstate\PhpExport\Order\AbstractOrder $orderObj
                                 */
                                $name = $orderObj->getName();
                                $titles[$name] = strtolower($orderObj->getTitle($environment->getLanguage()));
                                $orders[$name] = $orderObj;
                            }
                            asort($titles);
                            foreach (array_keys($titles) as $name) {
                                $selected = ($indexSettings['order_by'] === $name) ? 'selected="selected"' : '';
                                echo '<option value="' . html($name) . '" ' . $selected . '>'
                                    . html($orders[$name]->getTitle($environment->getLanguage()))
                                    . '</option>';
                            }
                            ?>
                        </select><br>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <select name="index[order_dir]">
                            <option value="asc" <?= ($indexSettings['order_dir'] == 'asc') ? 'selected="selected"' : '' ?>>
                                <?= html($module_i18n['view_index_order_asc']) ?>
                            </option>
                            <option value="desc" <?= ($indexSettings['order_dir'] == 'desc') ? 'selected="selected"' : '' ?>>
                                <?= html($module_i18n['view_index_order_desc']) ?>
                            </option>
                        </select>
                    </td>
                </tr>

                <?php
                $filters = array();
                $titles = array();
                foreach ($environment->getConfig()->getFilterObjects() as $filterObj) {
                    /**
                     * filter instance
                     * @var \OpenEstate\PhpExport\Filter\AbstractFilter $filterObj
                     */
                    $name = $filterObj->getName();
                    $filters[$name] = $filterObj;
                    $titles[$name] = strtolower($filterObj->getTitle($environment->getLanguage()));
                }
                asort($titles);
                foreach (array_keys($titles) as $name) {
                    /**
                     * filter instance
                     * @var \OpenEstate\PhpExport\Filter\AbstractFilter $filterObj
                     */
                    $filterObj = $filters[$name];
                    $filterValue = (isset($settings) && isset($settings['immotool_index']['filter'][$name])) ?
                        $settings['immotool_index']['filter'][$name] : '';

                    // create filter widget
                    $filterWidget = $filterObj->getWidget($environment, $filterValue);
                    $filterWidget->id = 'openestate_index_filter_' . $name;
                    $filterWidget->name = 'index[filter][' . $name . ']';
                    ?>
                    <tr>
                        <td>
                            <label for="<?= html($filterWidget->id) ?>">
                                <?= sprintf(
                                    html($module_i18n['view_index_filter']),
                                    '<q>' . html($filterObj->getTitle($environment->getLanguage())) . '</q>'
                                ) ?>
                            </label>
                        </td>
                        <td>
                            <?= $filterWidget->generate() ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>

            </table>

            <h3>
                <label>
                    <input type="radio" name="env_script" value="expose"
                           onchange="show_wrapper_settings('expose');" <?= ($envScript === 'expose') ? 'checked="checked"' : '' ?>/>
                    <?= html($module_i18n['view_expose']) ?> / expose.php
                </label>
            </h3>
            <table id="openestate_expose_settings" class="openestate-wrapper-admin"
                   style="visibility:<?= ($envScript === 'expose') ? 'visible' : 'collapse' ?>;">

                <tr>
                    <td>
                        <label for="openestate_expose_id"><?= html($module_i18n['view_expose_id']) ?></label>
                    </td>
                    <td>
                        <input id="openestate_expose_id" name="expose[id]" type="text" maxlength="100"
                               value="<?= html($exposeSettings['id']) ?>"/>
                    </td>
                </tr>

            </table>

            <h3>
                <label>
                    <input type="radio" name="env_script" value="fav"
                           onchange="show_wrapper_settings('fav');" <?= ($envScript === 'fav') ? 'checked="checked"' : '' ?>/>
                    <?= html($module_i18n['view_fav']) ?> / fav.php
                </label>
            </h3>
            <table id="openestate_fav_settings" class="openestate-wrapper-admin"
                   style="visibility:<?= ($envScript === 'fav') ? 'visible' : 'collapse' ?>;">

                <tr>
                    <td>
                        <label for="openestate_fav_view"><?= html($module_i18n['view_fav_view']) ?></label>
                    </td>
                    <td>
                        <select id="openestate_fav_view" name="fav[view]">
                            <option value="detail" <?= ($favSettings['view'] == 'detail') ? 'selected="selected"' : '' ?>>
                                <?= html($module_i18n['view_fav_view_detail']) ?>
                            </option>
                            <option value="thumb" <?= ($favSettings['view'] == 'thumb') ? 'selected="selected"' : '' ?>>
                                <?= html($module_i18n['view_fav_view_thumb']) ?>
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="openestate_fav_order_by"><?= html($module_i18n['view_fav_order']) ?></label>
                    </td>
                    <td>
                        <select id="openestate_fav_order_by" name="fav[order_by]">
                            <?php
                            $orders = array();
                            $titles = array();
                            foreach ($environment->getConfig()->getOrderObjects() as $orderObj) {
                                /**
                                 * order instance
                                 * @var \OpenEstate\PhpExport\Order\AbstractOrder $orderObj
                                 */
                                $name = $orderObj->getName();
                                $titles[$name] = strtolower($orderObj->getTitle($environment->getLanguage()));
                                $orders[$name] = $orderObj;
                            }
                            asort($titles);
                            foreach (array_keys($titles) as $name) {
                                $selected = ($favSettings['order_by'] === $name) ? 'selected="selected"' : '';
                                echo '<option value="' . html($name) . '" ' . $selected . '>'
                                    . html($orders[$name]->getTitle($environment->getLanguage()))
                                    . '</option>';
                            }
                            ?>
                        </select><br>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <select name="fav[order_dir]">
                            <option value="asc" <?= ($favSettings['order_dir'] == 'asc') ? 'selected="selected"' : '' ?>>
                                <?= html($module_i18n['view_fav_order_asc']) ?>
                            </option>
                            <option value="desc" <?= ($favSettings['order_dir'] == 'desc') ? 'selected="selected"' : '' ?>>
                                <?= html($module_i18n['view_fav_order_desc']) ?>
                            </option>
                        </select>
                    </td>
                </tr>

            </table>

            <h2><?= html($module_i18n['options']) ?></h2>
            <table class="openestate-wrapper-admin">

                <tr>
                    <td>
                        <label for="openestate_language"><?= html($module_i18n['options_language']) ?></label>
                    </td>
                    <td>
                        <select id="openestate_language" name="language">
                            <?php
                            foreach ($environment->getLanguageCodes() as $code) {
                                $selected = ($settings['language'] === $code) ? 'selected="selected"' : '';
                                echo '<option value="' . $code . '" ' . $selected . '>'
                                    . html($environment->getLanguageName($code))
                                    . '</option>';
                            }
                            ?>
                        </select><br>
                        <em><?= html($module_i18n['options_language_info']) ?></em>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="openestate_charset"><?= html($module_i18n['options_charset']) ?></label>
                    </td>
                    <td>
                        <input id="openestate_charset" name="charset" type="text" maxlength="100"
                               value="<?= html($settings['charset']) ?>"/><br>
                        <em><?= html($module_i18n['options_charset_info']) ?></em>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="openestate_listingUrl"><?= html($module_i18n['options_listingUrl']) ?></label>
                    </td>
                    <td>
                        <input id="openestate_listingUrl" name="listingUrl" type="text" maxlength="255"
                               value="<?= html($settings['listingUrl']) ?>"/><br>
                        <em><?= html($module_i18n['options_listingUrl_info']) ?></em>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="openestate_favUrl"><?= html($module_i18n['options_favUrl']) ?></label>
                    </td>
                    <td>
                        <input id="openestate_favUrl" name="favUrl" type="text" maxlength="255"
                               value="<?= html($settings['favUrl']) ?>"/><br>
                        <em><?= html($module_i18n['options_favUrl_info']) ?></em>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="openestate_exposeUrl"><?= html($module_i18n['options_exposeUrl']) ?></label>
                    </td>
                    <td>
                        <input id="openestate_exposeUrl" name="exposeUrl" type="text" maxlength="255"
                               value="<?= html($settings['exposeUrl']) ?>"/><br>
                        <em><?= html($module_i18n['options_exposeUrl_info']) ?></em>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="openestate_css"><?= html($module_i18n['options_css']) ?></label>
                    </td>
                    <td>
                        <textarea id="openestate_css" name="css"><?= html($settings['css']) ?></textarea><br>
                        <em><?= html($module_i18n['options_css_info']) ?></em>
                    </td>
                </tr>

                <tr>
                    <td>
                        <?= html($module_i18n['options_components']) ?>
                    </td>
                    <td>
                        <input type="hidden" name="allComponents"
                               value="<?= implode(',', $environment->getTheme()->getComponentIds()) ?>"/>
                        <?php foreach ($environment->getTheme()->getComponentIds() as $componentId) { ?>
                            <label style="margin-right:1em; white-space:nowrap;">
                                <input type="checkbox" id="openestate_component_<?= html($componentId) ?>"
                                       name="component[]" value="<?= html($componentId) ?>"
                                    <?= (!in_array($componentId, $settings['disabledComponents'])) ? 'checked="checked"' : '' ?>/>
                                <?= html($componentId) ?>
                            </label>
                        <?php } ?>
                        <br><em><?= html($module_i18n['options_components_info']) ?></em>
                    </td>
                </tr>

                <tr>
                    <td>
                        <?= html($module_i18n['options_features']) ?>
                    </td>
                    <td>
                        <label style="display:block; margin-bottom:0.5em;">
                            <input name="feature[]" type="checkbox" value="filtering"
                                <?= (in_array('filtering', $settings['features'])) ? 'checked="checked"' : '' ?>/>
                            <?= html($module_i18n['options_features_filtering']) ?>
                        </label>
                        <label style="display:block; margin-bottom:0.5em;">
                            <input name="feature[]" type="checkbox" value="ordering"
                                <?= (in_array('ordering', $settings['features'])) ? 'checked="checked"' : '' ?>/>
                            <?= html($module_i18n['options_features_ordering']) ?>
                        </label>
                        <label style="display:block; margin-bottom:0.5em;">
                            <input name="feature[]" type="checkbox" value="favorites"
                                <?= (in_array('favorites', $settings['features'])) ? 'checked="checked"' : '' ?>/>
                            <?= html($module_i18n['options_features_favorites']) ?>
                        </label>
                        <label style="display:block; margin-bottom:0.5em;">
                            <input name="feature[]" type="checkbox" value="languages"
                                <?= (in_array('languages', $settings['features'])) ? 'checked="checked"' : '' ?>/>
                            <?= html($module_i18n['options_features_languages']) ?>
                        </label>
                    </td>
                </tr>

            </table>

            <?php
        }
        ?>

        <div style="text-align: center;">
            <input name="save" type="submit" value="<?= $TEXT['SAVE'] ?>">
            <input type="button" value="<?= $TEXT['CANCEL'] ?>"
                   onclick="window.location = '<?= ADMIN_URL ?>/pages/index.php'; return false;">
        </div>
    </form>

    <div style="clear:both;"></div>
</div>