<?php

use local_academic_summary\form\formsummary;
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
 * @category    local
 * @copyright   2026 Renzo Medina <medinast30@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

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

if (!empty($username) || !empty($email)) {
    $user = $DB->get_record_sql($sql, ['username' => $username, 'email' => $email], IGNORE_MULTIPLE);
    if ($user) {
        $listusers[] = [
            'id' => $user->id,
            'fullname' => fullname($user),
            'email' => $user->email,
            'lastaccess' => userdate($user->lastaccess, get_string('strftimedatetime', 'langconfig')) ?: '',
            'totalcourses'   => 0,
            'averageprogress'=> 0,
        ];
    } else {
        \core\notification::add(get_string('nouserfound', 'local_academic_summary'), \core\notification::WARNING);
    }
}

echo $OUTPUT->header();
$templatedata =[
    'returnurl' => (new moodle_url('/admin/search.php#linkreports'))->out(),
    'formsummary' => $mform->render(),
    'users' => $listusers ?? [],
];
echo $OUTPUT->render_from_template('local_academic_summary/main', $templatedata);
echo $OUTPUT->footer();