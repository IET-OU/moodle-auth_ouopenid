<?php
/**
 * Base class.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 07-March-2018.
 * @copyright © 2018 The Open University.
 *
 * @link  https://docs.moodle.org/dev/Data_manipulation_API#Inserting_Records
 */

namespace auth_ouopenid\local;

class base {

  const BASE_KEY = 'auth_ouopenid';

  /**
   * @param string $key  Get plugin configuration for given key.
   * @param mixed  $default
   * @return mixed Configuration item (object, array, string...)
   */
  public static function config($key, $default = null) {
    if (isset($CFG->{ 'auth_ouopenid_' . $key })) {
      return $CFG->{ 'auth_ouopenid_' . $key };
    }
    self::debug([ __FUNCTION__, 'Missing or null value. Key / default: ', $key, $default ]);
    return $default;
  }

  /** Output arbitrary data, eg. to HTTP header.
  * @param mixed $obj  Array or object.
  */
  public static function debug($obj) {
      static $count = 0;
      header(sprintf('Xx-auth-ou-openid-%02d: %s', $count, json_encode($obj)));
      $count++;
  }
}
