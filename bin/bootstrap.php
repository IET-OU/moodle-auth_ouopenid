<?php
/**
 * Class for safe PSR-2 autoload and Moodle config file access.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 21-April-2017.
 * @copyright (c) 2017 The Open University.
 */
namespace auth_ouopenid;

use Dotenv;

class bootstrap {

    protected static function is_local_install() {
        return file_exists(__DIR__ . '/../vendor/autoload.php');
    }

    public static function vendor_autoload() {
        if (self::is_local_install()) {
            require_once(__DIR__ . '/../vendor/autoload.php');
        } else {
            require_once(__DIR__ . '/../../../vendor/autoload.php');
        }

    }

    public static function dotenv_load() {
        if (self::is_local_install()) {
            $dotenv = new \Dotenv\Dotenv(__DIR__ . '/..');
        } else {
            $dotenv = new \Dotenv\Dotenv(__DIR__ . '/../../..');
        }
        return $dotenv->load();
    }

    public static function moodle_config() {
        if (! self::is_local_install()) {
            require_once(__DIR__ . '/../../../config.php');
        }
    }

    public static function moodle_lib($libname) {
        if (! self::is_local_install()) {
            require_once($CFG->libdir . $libname . '.php');
        }
    }
}
