<?php
/**
 * "Complete" a "mod/assign" conditional activity containing a survey embedded via <IFRAME>.
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 19-February-2018.
 * @copyright © 2018 The Open University.
 *
 * @link  https://docs.moodle.org/dev/Data_manipulation_API#Inserting_Records
 */

namespace auth_ouopenid\local;

class conditional_embedded_survey {

  const CONFIG_KEY = 'auth_ouopenid_conditional_survey_activity';
  const MOD_TYPE_NAME = 'assign';   // 'mod/assign'
  const MOD_TYPE_ID = 1;            // ID in 'mdl_modules' table.
  const EMBED_LIKE = '%</iframe>%'; // MySQL 'LIKE'

  protected $course_id;   // 4,
  protected $course_code; // 'FR',
  protected $cmid;        // 72,
  protected $activity_id; // 'assign.id' = 13,

  protected $userid;
  protected $config;

  public function __construct( $course_code, $userid = null ) {
    $this->set_config( $course_code, $userid );
  }

  protected function set_config( $course_code, $userid = null ) {
    global $CFG, $USER; // Moodle globals;

    if (! isset( $CFG->{ self::CONFIG_KEY } )) {
      throw new \Exception( 'Missing configuration array:  \$CFG->' . self::CONFIG_KEY );
    }

    if (isset( $CFG->{ self::CONFIG_KEY }[ $course_code ] )) {
      $this->config = $config = $CFG->{ self::CONFIG_KEY }[ $course_code ];

      $this->course_id = $config->course_id;
      $this->course_code = $course_code;
      $this->cmid = $config->cmid;
      $this->activity_id = $config->activity_id;

      $this->userid = $userid ? $userid : $USER->id;

      self::_debug([ __METHOD__, $config, $this->userid ]);
    }
  }

  public function make_complete() {
    if ($this->is_valid_module() && $this->activity_has_embed()) {
      // return false;

      $this->assign_grades();
      $this->assign_submission();
      return $this->course_module_completion();
    }
    return false;
  }

  protected function is_valid_module() {
    global $DB; // Moodle global.

    $count = $DB->count_records( 'course_modules', [ 'id' => $this->cmid, 'module' => self::MOD_TYPE_ID,
        'course' => $this->course_id, 'instance' => $this->activity_id ]);

    self::_debug([ __METHOD__, $count ]); // '1' ??

    return 1 === $count;
  }

  protected function activity_has_embed() {
    global $DB; // Moodle global.

    $result = $DB->get_record_sql( 'SELECT * FROM {assign} WHERE ' . $DB->sql_like( 'intro', ':intro' ) . ' AND id = :id ',
        [ 'intro' => self::EMBED_LIKE, 'id' => $this->activity_id ]);

    self::_debug([ __METHOD__, $result ]);

    return $result;
  }

  protected function assign_grades() {
    global $DB; // Moodle global.

    $lastinsertid = $DB->insert_record('assign_grades', (object) [
      'assignment' => $this->activity_id,
      'userid' => $this->userid,
      'timecreated'  => time(), // UNIX_TIMESTAMP()
      'timemodified' => time(),
      'grader' => 0,
      'grade' => 100.00,
      'attemptnumber' => 0,
    ], false);

    self::_debug([ __METHOD__, $lastinsertid ]);
  }

  protected function assign_submission() {
    global $DB; // Moodle global.

    $lastinsertid = $DB->insert_record('assign_submission', (object) [
      'assignment' => $this->activity_id,
      'userid' => $this->userid,
      'timecreated'  => time(), // UNIX_TIMESTAMP()
      'timemodified' => time(),
      'status' => 'submitted',
      'groupid' => 0,
      'attemptnumber' => 0,
      'latest' => 1, // 'true'
    ], false);

    self::_debug([ __METHOD__, $lastinsertid ]);
  }

  protected function course_module_completion() {
    global $DB; // Moodle global.

    $lastinsertid = $DB->insert_record('course_module_completion', (object) [
      'coursemoduleid' => $this->cmid,
      'userid' => $this->userid,
      'compeletionstate' => 1, // 'true'
      'viewed' => 0, // 'false'
      'timemodified' => time(), // UNIX_TIMESTAMP()
    ], false);

    self::_debug([ __METHOD__, $lastinsertid ]);
  }

  /** Output arbitrary data, eg. to HTTP header.
  */
  protected static function _debug($obj)
  {
      static $count = 0;
      header(sprintf('X-auth-ou-cond-%02d: %s', $count, json_encode($obj)));
      $count++;
  }
}
