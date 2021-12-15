<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();


function intelliboard_pf_user($userid)
{
    global $DB;

    return $DB->get_record_sql("SELECT u.id, u.firstname, u.lastname
      FROM {user} u
        WHERE u.id = :userid", ['userid' => $userid]);
}

function intelliboard_pf_profile($userid, $courseid)
{
    global $DB;

    return $DB->get_record_sql("SELECT u.id, u.firstname, u.lastname, c.fullname
      FROM {user} u, {course} c
        WHERE u.id = :userid AND c.id = :courseid", ['userid' => $userid, 'courseid' => $courseid]);
}
function intelliboard_pf_cohorts()
{
    global $DB, $USER;

    return $DB->get_records_sql("SELECT c.id, c.name
      FROM {cohort_members} m, {cohort} c
        WHERE idnumber <> 'ClubManager' AND idnumber <> 'RegionalManager' AND m.userid = :userid AND c.id = m.cohortid", ['userid' => $USER->id]);
}
function intelliboard_pf_cohort()
{
    global $DB, $USER;

    if (isset($USER->pcid) and $USER->pcid) {
      return $DB->get_record("cohort", ['id' => $USER->pcid]);
    }
    return $DB->get_record_sql("SELECT c.id, c.name
      FROM {cohort_members} m, {cohort} c
        WHERE idnumber <> 'ClubManager' AND idnumber <> 'RegionalManager' AND m.userid = :userid AND c.id = m.cohortid LIMIT 1", ['userid' => $USER->id]);
}
function intelliboard_pf_fields($id)
{
    global $DB;

    return $DB->get_records_sql("
      SELECT MAX(d.id) AS id, MAX(d.fieldid) AS fieldid, MAX(f.name) AS name, d.data
        FROM {user_info_data} d
        JOIN {local_profilecohort} p ON d.fieldid = p.fieldid and p.value = :id
        JOIN {user_info_field} f ON f.id = d.fieldid
      WHERE data <> '' GROUP BY d.data", ['id' => $id]);
}

function intelliboard_pf_courses()
{
  global $DB;

  return $DB->get_records_sql("SELECT c.id, c.fullname, c.visible, c.category, ca.visible AS category_visible, ca.name FROM {course} c, {course_categories} ca WHERE c.category = ca.id");
}
