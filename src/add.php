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

// add page settings into the database
$database->query("INSERT INTO `$module_table` "
    . "(`page_id`, `section_id`) "
    . "VALUES ($page_id, $section_id)");
