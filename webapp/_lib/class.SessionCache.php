<?php
/**
 *
 * ThinkUp/webapp/_lib/class.SessionCache.php
 *
 * Copyright (c) 2011-2015 Gina Trapani
 *
 * LICENSE:
 *
 * This file is part of ThinkUp (http://thinkup.com).
 *
 * ThinkUp is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any
 * later version.
 *
 * ThinkUp is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with ThinkUp.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * SessionCache
 *
 * PHP $_SESSION accessor.
 *
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2011-2015 Gina Trapani
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 *
 */
class SessionCache {
    /**
     * Start the session system running. If use_db_sessions is set to true in the config file, store session data
     * in the datbase.
     * @return void
     */
    public static function init() {
        $config = Config::getInstance();
        if ($config->getValue('use_db_sessions')) {
            $session_dao = DAOFactory::getDAO('SessionDAO');
            session_set_save_handler(
                array($session_dao, 'open'),
                array($session_dao, 'close'),
                array($session_dao, 'read'),
                array($session_dao, 'write'),
                array($session_dao, 'destroy'),
                array($session_dao, 'gc')
            );
            // the following prevents unexpected effects when using objects as save handlers
            register_shutdown_function('session_write_close');
        }
        session_start();
    }

    /**
     * Put a value in ThinkUp's $_SESSION key.
     * @param str $key
     * @param str $value
     */
    public static function put($key, $value) {
        $config = Config::getInstance();
        $_SESSION[$config->getValue('source_root_path')][$key] = $value;
    }

    /**
     * Get a value from ThinkUp's $_SESSION.
     * @param str $key
     * @return mixed Value
     */
    public static function get($key) {
        $config = Config::getInstance();
        if (self::isKeySet($key)) {
            return $_SESSION[$config->getValue('source_root_path')][$key];
        } else {
            return null;
        }
    }

    /**
     * Check if a key in ThinkUp's $_SESSION has a value set.
     * @param str $key
     * @return bool
     */
    public static function isKeySet($key) {
        $config = Config::getInstance();
        return isset($_SESSION[$config->getValue('source_root_path')][$key]);
    }

    /**
     * Unset key's value in ThinkUp's $_SESSION
     * @param str $key
     */
    public static function unsetKey($key) {
        $config = Config::getInstance();
        unset($_SESSION[$config->getValue('source_root_path')][$key]);
    }
}
