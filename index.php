<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Main plugin file.
 *
 * @package     local_academic_summary
 * @copyright   2026 Renzo Medina <medinast30@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/completionlib.php');
require_once($CFG->dirroot . '/calendar/lib.php');
require_once($CFG->dirroot . '/lib/enrollib.php');
require_login();
require_capability('moodle/site:config', context_system::instance());
require_capability('local/academic_summary:view', context_system::instance());

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/academic_summary/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_academic_summary'));
$PAGE->set_heading(get_string('pluginname', 'local_academic_summary'));

use local_academic_summary\form\summary;

$mform = new summary();

$username = optional_param('username', '', PARAM_TEXT);
$email = optional_param('email', '', PARAM_EMAIL);
$listusers = [];
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/search.php#linkreports'));
} else if ($data = $mform->get_data()) {
    $username = $data->username;
    $email = $data->email;
}

$sql = "SELECT u.id, u.firstname, u.lastname, u.email, u.lastaccess
        FROM {user} u
        WHERE u.firstname = :username OR u.email = :email";
$coursedata = [];
$timestart = time();
$eventsdata = [];
$timeend = $timestart + (7 * DAYSECS);
if (!empty($username) || !empty($email)) {
    $user = $DB->get_record_sql($sql, ['username' => $username, 'email' => $email], IGNORE_MULTIPLE);
    $course = enrol_get_users_courses ($user->id, true, 'id, fullname, startdate, enddate');
    foreach ($course as $c) {
        $completion = new completion_info($c);
        $hascompletion = $completion->is_enabled();
        $percentage = 0;
        if ($hascompletion) {
            $modinfo = get_fast_modinfo($c);
            $total = 0;
            $completed = 0;
            foreach ($modinfo->cms as $cm) {
                if ($cm->completion == COMPLETION_TRACKING_NONE) {
                    continue;
                }
                $total++;
                $details = \core_completion\cm_completion_details::get_instance($cm, $user->id, true);

                if ($details->is_overall_complete()) {
                    $completed++;
                }
            }
            $percentage = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
        }

        $apitevents = \core_calendar\local\api::get_events(
            null, null, $timestart, $timeend, null, null, 20, null, null, null, [$c->id]
        );
        foreach ($apitevents as $event) {
            $eventsdata[] = [
                'eventname' => format_string($event->get_course()->get('fullname')) . ' — ' . $event->get_name(),
                'eventdate' => userdate(
                    $event->get_times()->get_start_time()->getTimestamp(),
                    get_string('strftimedatetime', 'langconfig')
                ),
            ];
        }
        $coursedata[] = [
            'coursename' => format_string($c->fullname),
            'startdate' => userdate($c->startdate, get_string('strftimedate', 'langconfig')),
            'enddate' => !empty($c->enddate) ? userdate($c->enddate,
            get_string('strftimedate', 'langconfig')) : get_string('noenddate', 'local_academic_summary'),
            'hascompletion' => $hascompletion,
            'iscompleted' => $hascompletion ? ($percentage >= 100) : false,
            'isinprogress' => $hascompletion ? ($percentage > 0 && $percentage < 100) : false,
            'isnotstarted' => $hascompletion ? ($percentage <= 0) : false,
            'isnotactive' => !$hascompletion,
            'percentage' => $percentage,
            'linkcourse' => (new moodle_url('/course/view.php', ['id' => $c->id]))->out(false),
        ];
    }
    if ($user) {
        $listusers[] = [
            'userid' => $user->id,
            'fullname' => fullname($user),
            'email' => $user->email,
            'lastaccess' => userdate($user->lastaccess, get_string('strftimedatetime', 'langconfig')) ?: '',
            'totalcourses'   => count($coursedata),
            'averageprogress' => $coursedata ? array_sum(array_column($coursedata, 'percentage')) / count($coursedata) : 0,
            'courses' => $coursedata ?? [],
            'upcomingevents' => !empty($eventsdata),
            'events' => $eventsdata ?? [],
        ];
    } else {
        \core\notification::add(get_string('nouserfound', 'local_academic_summary'), \core\notification::WARNING);
    }
}

echo $OUTPUT->header();
$templatedata = [
    'returnurl' => (new moodle_url('/admin/search.php#linkreports'))->out(),
    'formsummary' => $mform->render(),
    'users' => $listusers ?? [],
];
echo $OUTPUT->render_from_template('local_academic_summary/main', $templatedata);
echo $OUTPUT->footer();
