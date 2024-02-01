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
 * User interface for the local_completion_report plugin.
 *
 * @package   local_completion_report
 * @copyright 2024, Szilard Szabo <szilard22@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('lib.php');

require_login();

if (!is_siteadmin()) {
    redirect(new moodle_url('/login/index.php'));
}

admin_externalpage_setup('localcompletionreport', '', null, '', ['pagelayout' => 'report']);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/completion_report/index.php');
$PAGE->set_title(get_string('pluginname', 'local_completion_report'));
$PAGE->set_heading(get_string('pluginname', 'local_completion_report'));

echo $OUTPUT->header();

$report = new LocalCompletionReport($DB);

$userid = optional_param('userid', 0, PARAM_INT);

if ($userid) {
    $completions = $report->get_user_course_completions($userid);
    $table = new html_table();
    $table->head = ['Course Name', 'Status', 'Completion Date'];
    foreach ($completions as $completion) {
        $date = $completion->timecompleted ?
            userdate($completion->timecompleted, get_string('strftimedatetime', 'langconfig')) :
            get_string('na', 'local_completion_report');
        $status = $completion->completed ?
            get_string('completed', 'local_completion_report') :
            get_string('not_completed', 'local_completion_report');
        $coursename = html_writer::link(
            new moodle_url('/course/view.php', ['id' => $completion->courseid]),
            $completion->coursename
        );
        $table->data[] = [$coursename, $status, $date];
    }
    echo html_writer::tag('h4', "User: {$completion->username}");
    echo html_writer::table($table);
    $button = new single_button(
        new moodle_url('/local/completion_report/index.php'),
        '<< Back', 'get'
    );
    $button = $OUTPUT->render($button);
    echo html_writer::tag('div', $button);
} else {
    $users = $report->get_users();
    $table = new html_table();
    $table->head = ['Username', 'First name', 'Last name', 'Email'];
    foreach ($users as $user) {
        $url = new moodle_url('/local/completion_report/index.php', ['userid' => $user->id]);
        $link = html_writer::link($url, $user->username);
        $table->data[] = [$link, $user->firstname, $user->lastname, $user->email];
    }
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
