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

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/search.php#linkusers'));
} else if ($data = $mform->get_data()) {
    // Process form data if needed.
}

echo $OUTPUT->header();
$templatedata =[
    'returnurl' => (new moodle_url('/admin/search.php#linkusers'))->out(),
    'formsummary' => $mform->render(),
];
echo $OUTPUT->render_from_template('local_academic_summary/main', $templatedata);
echo $OUTPUT->footer();