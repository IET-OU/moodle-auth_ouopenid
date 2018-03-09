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

use auth_ouopenid\local\base;

class conditional_embedded_survey extends base {

  const CONFIG_KEY = self::BASE_KEY . '_conditional_survey_activity';
  const MOD_TYPE_NAME = 'assign';   // 'mod/assign'
  const MOD_TYPE_ID = 1;            // ID in 'mdl_modules' table.
  const GRADER_USER_ID = 2;         // User ID, not '0' !!
  // const EMBED_LIKE = '%</iframe>%'; // MySQL 'LIKE'
  const EMBED_REGEXP = '(<\/iframe>|[?#!]-pre-survey-embed)'; // MySQL 'REGEXP'

  protected $course_id;   // 4,
  protected $course_code; // 'FR',
  protected $cmid;        // 72,
  protected $activity_id; // 'assign.id' = 13,
  protected $grade_items_id; // 47,

  protected $userid;
  protected $config;

  /**
   * @param string $course_code Course shortname, example, 'FR' or 'TPT'.
   * @param int    $userid  Moodle user ID (or username).
   */
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
      $this->grade_items_id = $config->grade_items_id;

      $this->userid = $userid ? $userid : $USER->id;

      self::debug([ __METHOD__, $config, $this->userid ]);
    } else {
      throw new \Exception(sprintf( 'Missing course code in configuration:  \$CFG->%s, %s', self::CONFIG_KEY, $course_code ));
    }
  }

  public function make_complete() {
    $b_ok = false;
    if ($this->is_valid_module() && $this->activity_has_embed()) {
      try {
        $this->assign_grades();
        $this->assign_submission();
      } catch (\dml_write_exception $ex) {
        self::debug([ __FUNCTION__, 'dml_write_exception', $ex->getMessage(), $ex->debuginfo ]);

        if (! preg_match('/Duplicate entry .+/', $ex->debuginfo)) {
          throw $ex;
        }
      }
      $b_ok = $this->course_modules_complete();

      $this->_grade_update();

      return $b_ok;
    }
    return false;
  }

  public function un_complete() {
    if ($this->is_valid_module() && $this->activity_has_embed()) {
      $this->un_assign_grades();
      $this->un_assign_submission();
      return $this->course_modules_un_complete();
    }
    return false;
  }

  protected function is_valid_module() {
    global $DB; // Moodle global.

    $count = $DB->count_records( 'course_modules', [ 'id' => $this->cmid, 'module' => self::MOD_TYPE_ID,
        'course' => $this->course_id, 'instance' => $this->activity_id ]);

    self::debug([ __METHOD__, $count ]); // '1' ??
    return 1 === $count;
  }

  protected function activity_has_embed() {
    global $DB; // Moodle global.

    $result = $DB->get_record_sql( 'SELECT * FROM {assign} WHERE intro REGEXP :intro_re AND id = :id ',
        [ 'intro_re' => self::EMBED_REGEXP, 'id' => $this->activity_id ]);

    // Was: $result = $DB->get_record_sql( 'SELECT * FROM {assign} WHERE ' . $DB->sql_like( 'intro', ':intro' ) . ' AND id = :id ',
    //    [ 'intro' => self::EMBED_LIKE, 'id' => $this->activity_id ]);

    self::debug([ __METHOD__, $result ]);
    return $result;
  }

  protected function assign_grades() {
    global $DB; // Moodle global.

    $lastinsertid = $DB->insert_record('assign_grades', (object) [
      'assignment' => $this->activity_id,
      'userid' => $this->userid,
      'timecreated'  => time(), // UNIX_TIMESTAMP()
      'timemodified' => time(),
      'grader' => self::GRADER_USER_ID,
      'grade' => 100.00,
      'attemptnumber' => 0,
    ], false);

    self::debug([ __METHOD__, $lastinsertid ]);
  }

  protected function un_assign_grades() {
    global $DB;
    return $DB->delete_records('assign_grades', [
      'assignment' => $this->activity_id,
      'userid' => $this->userid,
    ]);
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

    self::debug([ __METHOD__, $lastinsertid ]);
  }

  protected function un_assign_submission() {
    global $DB;
    return $DB->delete_records('assign_submission', [
      'assignment' => $this->activity_id,
      'userid' => $this->userid,
    ]);
  }

  protected function course_modules_complete() {
    // https://docs.moodle.org/dev/Activity_completion_API#Notifying_the_completion_system
    // https://github.com/moodle/moodle/blob/master/lib/completionlib.php#L532-L565
    // https://github.com/moodle/moodle/blob/master/lib/modinfolib.php#L1835

    $cminfo = \cm_info::create( (object) [ 'id' => $this->cmid, 'course' => $this->course_id ], $this->userid );

    $completion = new \completion_info(\get_course($this->course_id));
    // $result = $completion->update_state($cminfo, COMPLETION_COMPLETE, $this->userid);  // , $override = true);

    $data = $completion->get_data($cminfo, false, $this->userid);
    $data->completionstate = COMPLETION_COMPLETE;
    $data->timemodified = time();
    $data->overrideby = $this->userid;
    $completion->internal_set_data($cminfo, $data);

    self::debug([ __FUNCTION__, 'completion->internal_set_data() (update_state)' ]);
  }

  protected function _X_OLD_course_modules_complete() {
    global $DB; // Moodle global.

    $lastinsertid = $DB->insert_record('course_modules_completion', (object) [
      'coursemoduleid' => $this->cmid,
      'userid' => $this->userid,
      'completionstate' => 1, // 'true'
      'viewed' => 0, // 'false'
      'timemodified' => time(), // UNIX_TIMESTAMP()
    ], false);

    self::debug([ __METHOD__, $lastinsertid ]);
  }

  protected function course_modules_un_complete() {
    global $DB;
    return $DB->delete_records('course_modules_completion', [
      'coursemoduleid' => $this->cmid,
      'userid' => $this->userid,
    ]);
  }

  protected function _X_OLD_set_grade_grades() {
    global $DB;
    $grade = $DB->get_record('grade_grades', [
      'itemid' => $this->grade_items_id,
      'userid' => $this->userid,
    ]);
    self::debug([ __METHOD__, 'get', $grade ]);
    if ($grade) {
      $grade->rawgrade = 100.00;
      $grade->finalgrade = 100.00;
      $grade->timemodified = time();

      $DB->update_record('grade_grades', $grade);

      self::debug([ __METHOD__, 'update', $grade ]);
    }
  }

  // https://github.com/moodle/moodle/blob/master/lib/gradelib.php#L61
  protected function _grade_update() {
    $gradestructure = [
      'userid' => $this->userid,
      'rawgrade' => 100.00,
      'finalgrade' => 100.00,
      'feedback' => '[auto-submit]' . __CLASS__,
      'feedbackformat' => FORMAT_MOODLE,   // '1'.
      'datesubmitted' => time(),
      'dategraded' => time(),
    ];
    $result = \grade_update('mod/assign', $this->course_id, 'mod', 'assign', $this->activity_id, 0, $gradestructure, null); // Not: 'grade_items_id'

    self::debug([ __FUNCTION__, 'mod/assign', $this->activity_id, $result, GRADE_UPDATE_OK == $result ? 'OK' : 'FAIL' ]);
  }

  public function get_course_modules_completion() {
    global $DB;
    return $DB->get_record('course_modules_completion', [
      'coursemoduleid' => $this->cmid,
      'userid' => $this->userid,
    ]);
  }
}
