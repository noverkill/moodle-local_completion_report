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
 * Setting page for the local_completion_report plugin.
 *
 * @package   local_completion_report
 * @copyright 2024, Szilard Szabo <szilard22@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;

if ($hassiteconfig) {
    $settingspage = new admin_externalpage(
        'localcompletionreport',
        get_string('pluginname', 'local_completion_report'),
        "$CFG->wwwroot/local/completion_report/index.php",
    );

    $ADMIN->add('reports', $settingspage);
}
