<?php
/**
 * Allow a student to "consent" to the TeSLA pilot study, via an embedded survey.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 02-March-2018.
 * @copyright © 2018 The Open University.
 *
 * @link  https://docs.moodle.org/dev/Data_manipulation_API#Inserting_Records
 */

namespace auth_ouopenid\local;

use auth_ouopenid\local\base;

class tesla_consent extends base {

  const DB_TABLE = 'local_tesla_agreement';
  const AGREE_VERSION = '2.0';

  // const CONFIG_KEY = 'auth_ouopenid_tesla_consent';

  /** Has the user already consented?
   * @param int $userid
   */
  public static function has_agreed( $userid = null ) {
    global $DB, $USER; // Moodle global.

    $userid = $userid ? $userid : $USER->id;

    return $DB->count_records( self::DB_TABLE, [ 'userid' => $userid ]);
  }

  /** The user consents to the agreement.
   * @param int $userid
   * @param string $version  Agreement version.
   * @return int  The inserted agreement ID.
   */
  public static function agree( $userid = null, $version = self::AGREE_VERSION ) {
    global $DB, $USER; // Moodle global.

    $userid = $userid ? $userid : $USER->id;

    $lastinsertid = $DB->insert_record(self::DB_TABLE, (object) [
      'userid' => $userid,
      'timesubmitted' => time(), // UNIX_TIMESTAMP()
      'version' => $version,
      'accepted' => true,
    ], false);

    self::_debug([ __METHOD__, $lastinsertid ]);

    return $lastinsertid;
  }

  /* [1241] => stdClass Object (
      [id] => 1241
      [userid] => 5
      [timesubmitted] => 1519734197
      [version] => 2.0
      [accepted] => 1
      [timesubmitted_iso] => 2018-02-27T12:23:17+00:00
  ) */
}
