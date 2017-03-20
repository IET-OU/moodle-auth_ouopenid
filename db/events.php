<?php
/**
 * Event observers.
 *
 * @author Nick Freear, 17-March-2017.
 */

$observers = [
  [
    'eventname' => '\core\event\user_created',
    'callback'  => '\auth_ouopenid\user_event_observer::core_user_created',
  ],
  [
    'eventname' => '\core\event\user_loggedin',
    'callback'  => '\auth_ouopenid\user_event_observer::core_user_loggedin',
  ]
];

//End.
