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
 * Version metadata for the local_academic_summary plugin.
 *
 * @package   local_academic_summary
 * @copyright 2026, Renzo Medina <medinast30@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_academic_summary\form;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . '/formslib.php');
/**
 * Form definition for the academic summary report.
 * @package   local_academic_summary
 * @copyright 2026, Renzo Medina <medinast30@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class summary extends \moodleform {
    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        // this form is simple, just to show how to use the template, you can add more fields as needed.
        $mform->addElement('text', 'username', get_string('username', 'local_academic_summary'), ['size' => 50]);
        $mform->setType('username', PARAM_TEXT);
        $mform->addHelpButton('username',  'username',  'block_extsearch');
        $mform->addElement('text', 'email', get_string('email', 'local_academic_summary'), ['size' => 50]);
        $mform->setType('email', PARAM_EMAIL);
        $mform->addHelpButton('email',  'email',  'block_extsearch');
        $mform->addElement('submit', 'submit', get_string('search', 'local_academic_summary'));
    }
}

