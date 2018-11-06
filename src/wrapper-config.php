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

namespace OpenEstate\PhpExport;

/**
 * Extended configuration for integration into the website.
 *
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2018, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License 2 (or later)
 */
class WrapperConfig extends MyConfig
{
    /**
     * Wrapper settings.
     *
     * @var array
     */
    public $wrapperSettings = array();

    public function __construct($basePath, $baseUrl, $wrapperSettings)
    {
        parent::__construct($basePath, $baseUrl);

        $this->wrapperSettings = $wrapperSettings;

        // set configured charset
        if (isset($this->wrapperSettings['charset'])) {
            $charset = \trim($this->wrapperSettings['charset']);
            if (Utils::isNotBlankString($charset)) {
                $this->charset = $charset;
            }
        }

        // enable / disable favorites
        $this->favorites = isset($this->wrapperSettings['features'])
            && \is_array($this->wrapperSettings['features'])
            && \in_array('favorites', $this->wrapperSettings['features']);

        // enable / disable language selection
        $this->allowLanguageSelection = isset($this->wrapperSettings['features'])
            && \is_array($this->wrapperSettings['features'])
            && \in_array('languages', $this->wrapperSettings['features']);
    }

    public function getActionUrl($parameters = null)
    {
        if ($parameters == null) {
            $parameters = array();
        }

        if (isset($this->wrapperSettings['section_id']))
            $parameters['section'] = $this->wrapperSettings['section_id'];

        return WB_URL . '/modules/' . \basename(__DIR__) . '/wrapper-action.php'
            . Utils::getUrlParameters($parameters);
    }

    public function getExposeUrl($parameters = null)
    {
        $baseUrl = (isset($this->wrapperSettings['exposeUrl']))?
            \trim($this->wrapperSettings['exposeUrl']): null;

        if (Utils::isBlankString($baseUrl)) {
            $baseUrl = \explode('?', $_SERVER['REQUEST_URI'])[0];
        }

        if ($parameters == null) {
            $parameters = array();
        }

        $parameters['wrap'] = 'expose';
        foreach ($_REQUEST as $key => $value) {
            if (!isset($parameters[$key]) && $key != 'update') {
                $parameters[$key] = $value;
            }
        }

        return $baseUrl . Utils::getUrlParameters($parameters);
    }

    public function getFavoriteUrl($parameters = null)
    {
        $baseUrl = (isset($this->wrapperSettings['favUrl']))?
            \trim($this->wrapperSettings['favUrl']): null;

        if (Utils::isBlankString($baseUrl)) {
            $baseUrl = \explode('?', $_SERVER['REQUEST_URI'])[0];
        }

        if ($parameters == null) {
            $parameters = array();
        }

        $parameters['wrap'] = 'fav';
        foreach ($_REQUEST as $key => $value) {
            if (!isset($parameters[$key]) && $key != 'update') {
                $parameters[$key] = $value;
            }
        }

        return $baseUrl . Utils::getUrlParameters($parameters);
    }

    public function getListingUrl($parameters = null)
    {
        $baseUrl = (isset($this->wrapperSettings['listingUrl']))?
            \trim($this->wrapperSettings['listingUrl']): null;

        if (Utils::isBlankString($baseUrl)) {
            $baseUrl = \explode('?', $_SERVER['REQUEST_URI'])[0];
        }

        if ($parameters == null) {
            $parameters = array();
        }

        $parameters['wrap'] = 'index';
        foreach ($_REQUEST as $key => $value) {
            if (!isset($parameters[$key]) && $key != 'update') {
                $parameters[$key] = $value;
            }
        }

        return $baseUrl . Utils::getUrlParameters($parameters);
    }

    public function newSession(Environment $env)
    {
        return new WrapperSession($env);
    }

    public function setupEnvironment(Environment $env)
    {
        parent::setupEnvironment($env);
        //Environment::$parameterPrefix = 'wrap';
    }

    public function setupExposeHtml(View\ExposeHtml $view)
    {
        parent::setupExposeHtml($view);
        $view->setBodyOnly(true);
    }

    public function setupFavoriteHtml(View\FavoriteHtml $view)
    {
        parent::setupFavoriteHtml($view);
        $view->setBodyOnly(true);

        // disable ordering
        $ordering = isset($this->wrapperSettings['features'])
            && \is_array($this->wrapperSettings['features'])
            && \in_array('ordering', $this->wrapperSettings['features']);
        if (!$ordering) $view->orders = array();
    }

    public function setupListingHtml(View\ListingHtml $view)
    {
        parent::setupListingHtml($view);
        $view->setBodyOnly(true);

        // disable ordering
        $ordering = isset($this->wrapperSettings['features'])
            && \is_array($this->wrapperSettings['features'])
            && \in_array('ordering', $this->wrapperSettings['features']);
        if (!$ordering) $view->orders = array();

        // disable filtering
        $filtering = isset($this->wrapperSettings['features'])
            && \is_array($this->wrapperSettings['features'])
            && \in_array('filtering', $this->wrapperSettings['features']);
        if (!$filtering) $view->filters = array();
    }

    public function setupTheme(Theme\AbstractTheme $theme)
    {
        parent::setupTheme($theme);

        // register disabled components
        if (isset($this->wrapperSettings['disabledComponents']) && is_array($this->wrapperSettings['disabledComponents'])) {
            foreach ($this->wrapperSettings['disabledComponents'] as $componentId) {
                $theme->setComponentEnabled($componentId, false);
            }
        }
    }
}

/**
 * Session handler for integration into WBCE.
 *
 * @author Andreas Rudolph & Walter Wagner
 * @copyright 2010-2018, OpenEstate.org
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License 2 (or later)
 */
class WrapperSession extends Session\PhpSession
{
    function __construct(Environment $env, $root = null)
    {
        parent::__construct($env, $root);
    }

    /**
     * Session is managed by WBCE.
     * Therefore we don't call session_start() here.
     */
    public function init()
    {
        if (!isset($_SESSION[$this->root]))
            $_SESSION[$this->root] = array();
    }

    /**
     * Session is managed by WBCE.
     * Therefore we don't do anything here.
     */
    public function write()
    {
    }
}
