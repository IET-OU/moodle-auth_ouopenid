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

  /** Output arbitrary data, eg. to HTTP header.
  */
  public static function debug($obj) {
      static $count = 0;
      header(sprintf('Xx-auth-ou-openid-%02d: %s', $count, json_encode($obj)));
      $count++;
  }
}
