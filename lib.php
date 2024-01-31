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
 * Library functions for the local_completion_report plugin.
 *
 * @package   local_completion_report
 * @copyright 2024, Szilard Szabo <szilard22@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/lib/accesslib.php');

class LocalCompletionReport {
    private $DB;

    public function __construct($DB) {
        $this->DB = $DB;
    }

    public function get_users() {
        $roles = get_archetype_roles('student');
        $roleids = array_map(function($role) { return $role->id; }, $roles);
        list($insql, $inparams) = $this->DB->get_in_or_equal($roleids);
    
        $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email 
                FROM {user} u 
                JOIN {user_enrolments} ue ON u.id = ue.userid 
                JOIN {enrol} e ON e.id = ue.enrolid
                JOIN {course} c ON c.id = e.courseid
                JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = ?
                JOIN {role_assignments} ra ON ra.userid = u.id AND ra.contextid = ctx.id
                WHERE ra.roleid $insql
                GROUP BY u.id
                ORDER BY u.username";
    
        $params = array_merge(array(CONTEXT_COURSE), $inparams);
    
        return $this->DB->get_records_sql($sql, $params);
    }

    public function get_user_course_completions($userid) {
        return $this->DB->get_records_sql(
            "SELECT u.id, u.username, c.id, c.fullname, cc.timecompleted, 
             CASE WHEN cc.course IS NULL THEN 0 ELSE 1 END AS completed 
             FROM {course} c 
             JOIN {enrol} e ON e.courseid = c.id
             JOIN {user_enrolments} ue ON ue.enrolid = e.id 
             JOIN {user} u ON u.id = ue.userid
             LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ue.userid 
             WHERE ue.userid = ?
             ORDER BY c.fullname",
             array($userid));
    }
}
