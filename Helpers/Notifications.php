<?php

namespace FFMVC\Helpers;


/**
 * Notifications Helper Class.
 *
 * @author Vijay Mahrra <vijay.mahrra@gmail.com>
 * @copyright (c) Copyright 2006-2015 Vijay Mahrra
 * @license GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 */
class Notifications extends \Prefab
{

    public static $TYPES = [
        'success',
        'danger',
        'warning',
        'info'
    ];

    final public static function init($saveState = true)
    {
        $f3 = \Base::instance();
        $cli = (PHP_SAPI == 'cli');
        $notifications = $f3->get('notifications');
        if (empty($notifications)) {
            $notifications = [];
        }
        foreach (self::$TYPES as $type) {
            if (!array_key_exists($type, $notifications)) {
                $notifications[$type] = [];
            }
        }
        $f3->set('notifications', $notifications);
        if (!$cli) {
            $f3->set('notifications_save_state', $saveState); // save notifications in session?
        }
    }


    /**
     * Clear all notifications
     */
    final public static function clear()
    {
        $f3 = \Base::instance();
        $notifications = [];
        foreach (self::$TYPES as $type) {
            $notifications[$type] = [];
        }
        $f3->set('notifications', $notifications);
    }


    final public static function saveState($boolean = true)
    {
        if (PHP_SAPI !== 'cli') {
            $f3 = \Base::instance();
            $f3->set('notifications_save_state', $boolean);
            return true;
        } else {
            return false;
        }
    }


    public function __destruct()
    {
        if (PHP_SAPI !== 'cli') {
            $f3 = \Base::instance();
            // save persistent notifications
            $saveState = $f3->get('notifications_save_state');
            $notifications = $f3->get('notifications');
            $f3->set('SESSION.notifications', empty($saveState) ? null : $notifications);
        }
    }


    // add a notification, default type is notification
    final public static function add($notification, $type = null)
    {
        $f3 = \Base::instance();
        $notifications = $f3->get('notifications');
        $type = (empty($type) || !in_array($type, self::$TYPES)) ? 'info' : $type;
        // don't repeat notifications!
        if (!in_array($notification, $notifications[$type]) && is_string($notification)) {
            $notifications[$type][] = $notification;
        }
        $f3->set('notifications', $notifications);
    }


    /**
     * add multiple notifications by type
     *
     * @param type $notificationsList
     */
    final public function addMultiple($notificationsList)
    {
        $f3 = \Base::instance();
        $notifications = $f3->get('notifications');
        foreach ($notificationsList as $type => $list) {
            foreach ($list as $notification) {
                $notifications[$type][] = $notification;
            }
        }
        $f3->set('notifications', $notifications);
    }


    // return notifications of given type or all TYPES, return false if none
    final public static function sum($type = null)
    {
        $f3 = \Base::instance();
        $notifications = $f3->get('notifications');
        if (!empty($type)) {
            if (in_array($type, self::$TYPES)) {
                $i = count($notifications[$type]);
                return $i;
            } else {
                return false;
            }
        }
        $i = 0;
        foreach (self::$TYPES as $type) {
            $i += count($notifications[$type]);
        }
        return $i;
    }


    // return notifications of given type or all TYPES, return false if none, clearing stack
    final public static function get($type = null, $clear = true)
    {
        $f3 = \Base::instance();
        $notifications = $f3->get('notifications');
        if (!empty($type)) {
            if (in_array($type, self::$TYPES)) {
                $i = count($notifications[$type]);
                if (0 < $i) {
                    return $notifications[$type];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        // return false if there actually are no notifications in the session
        $i = 0;
        foreach (self::$TYPES as $type) {
            $i += count($notifications[$type]);
        }
        if (0 == $i) {
            return false;
        }

        // order return by order of type array above
        // i.e. success, error, warning and then informational notifications last
        foreach (self::$TYPES as $type) {
            $return[$type] = $notifications[$type];
        }

        // clear all notifications
        if (!empty($clear)) {
            self::clear();
        }
        
        return $return;
    }


}