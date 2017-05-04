<?php
/**
 * OU-OpenID database upgrade (update) function(s).
 *
 * @package auth_ouopenid
 * @author  Nick Freear, 01-May-2017.
 * @copyright (c) 2017 The Open University.
 * @license proprietary
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_auth_ouopenid_upgrade($oldversion) {
    global $DB; // Moodle global.

    $TABLE_NAME = 'auth_ouopenid_users';
    $SAVE_POINT_1 = 2017042800;

    __auth_ouopenid_debug(__FUNCTION__);

    $dbman = $DB->get_manager();

    if ($oldversion < 2017042800) {

        // Create table for agreement.
        $table = new xmldb_table('auth_ouopenid_users');

        // Adding fields to table ..users.
        // $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        // $table->add_field('version', XMLDB_TYPE_CHAR, '5', null, null, null, null);

        // Adding keys to table local_tesla_agreement.
        // $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Add activity completed field
        // https://github.com/moodle/moodle/blob/master/lib/xmldb/xmldb_field.php#L95
        // _($name, $type=null, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $default=null, $previous=null)
        // <FIELD NAME="batch" TYPE="int" LENGTH="5" NOTNULL="false" DEFAULT="0" SEQUENCE="false" COMMENT=".."/>
        $field = new xmldb_field('batch', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0', null);

        if (! $dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            __auth_ouopenid_debug('add_field');
        }

        // update the data
        // $DB->execute("UPDATE {local_tesla_enrollment} SET done = '1' WHERE data = 'yes'");

        // Plugin savepoint reached.
        upgrade_plugin_savepoint(true, 2017042800, 'auth', 'ouopenid');

        __auth_ouopenid_debug('savepoint');
    }
}

function __auth_ouopenid_debug($obj) {
    static $count = 0;
    // ?? header(sprintf('X-auth-ou-openid-upgrade-%02d: %s', $count, json_encode($obj)));
    $count++;
}
