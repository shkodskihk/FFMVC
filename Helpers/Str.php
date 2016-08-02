<?php

namespace FFMVC\Helpers;

/**
 * String Helper Class
 *
 * @package helpers
 * @author Vijay Mahrra <vijay@yoyo.org>
 * @copyright (c) Copyright 2015 Vijay Mahrra
 * @license GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 */
class Str extends \Prefab
{
    /**
     * generate random string
     *
     * @param int $length of password
     * @param string $chars characters to use for random string
     * @return string password
     */
    public static function random($length = 10, $chars = null)
    {
        if (empty($chars)) {
            // ignore characters which can be consued, i, l, 1, o, O, 0 etc
            $chars = '23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWYZ';
        }

        $chars = str_shuffle($chars); // shuffle base character string
        $x = \UTF::instance()->strlen($chars) - 1;
        $str = '';

        for ($i = 0; $i < $length; $i++) {
            $str .= \UTF::instance()->substr($chars, rand(0, $x), 1);
        }

        return (string) $str;
    }


    /**
     * Generates a hash for a given string
     *
     * @param string $string to salt
     * @param string $pepper string pepper to add to the salted string for extra security
     * @param string $salt string if not default app.salt config item
     * @return string $encoded
     * @link http://php.net/manual/en/function.hash-hmac.php
     * @link http://fatfreeframework.com/base#hash
     */
    public static function salted($string, $pepper = '')
    {
        $f3 = \Base::instance();
        $salt = $f3->get('app.salt');
        $hash = $f3->get('app.hash');

        return base64_encode(hash_hmac($hash, $string, $salt . $pepper, true));
    }

    /**
     * Generates a hashed password a given string
     *
     * @param string $string to salt
     * @param string $pepper string pepper to add to the salted string for extra security
     * @return string $encoded
     */
    public static function password($string, $pepper = '')
    {
        return \Base::instance()->hash(self::salted($string, $pepper));
    }


    /**
     * Compares a hashed password with the hashed value of a given string
     *
     * @param string $hashed_password a hashed password
     * @param string $string to salt
     * @param string $pepper string pepper to add to the salted string for extra security
     * @return boolean success on match
     */
    public static function passwordVerify($hashed_password, $string, $pepper = '')
    {
        return ($hashed_password === \Base::instance()->hash(self::salted($string, $pepper)));
    }


    /**
     * generate uuid string
     *
     * @return string uuid
     */
    public static function uuid()
    {
        $faker = \Faker\Factory::create();

        return $faker->uuid;
    }

    /**
     * Deserialize a value as an object or array if serialized
     *
     * @param mixed $value
     */
    public static function deserialize($value)
    {
        // first try to unserialize php object
        $v = @unserialize($value); // object if success

            // next try to json_decode - results in array
        if (empty($v) || !is_object($v)) {
            $v = json_decode($value, true);
        }

        // update value to unserialized object/array if necessary
        if (is_object($v) || is_array($v)) {
            return $v;
        }

        return $value;
    }
}
