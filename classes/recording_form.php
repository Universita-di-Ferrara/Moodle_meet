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

namespace mod_gmeet;

/**
 * Class recording_form
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recording_form extends \core_form\dynamic_form {

    protected function get_context_for_dynamic_submission(): \context {
        return \context_system::instance();
    }

    protected function check_access_for_dynamic_submission(): void {
        require_capability('mod/gmeet:addinstance', $this->get_context_for_dynamic_submission());
    }

    public function set_data_for_dynamic_submission(): void {
        $this->set_data([
            'id' => $this->optional_param('id', '', PARAM_INT),
            'recordingname' => $this->optional_param('recordingname', '', PARAM_TEXT),
            'recordingdescription' => $this->optional_param('recordingdescription', '', PARAM_TEXT),
        ] + $this->get_options());
    }

    public function process_dynamic_submission() {
        return $this->get_data();
    }
    protected function get_options(): array {
        $rv = [];
        if (!empty($this->_ajaxformdata['option']) && is_array($this->_ajaxformdata['option'])) {
            foreach (array_values($this->_ajaxformdata['option']) as $idx => $option) {
                $rv["option[$idx]"] = clean_param($option, PARAM_CLEANHTML);
            }
        }
        return $rv;
    }


    public function definition() {
        $mform = $this->_form;

        // Required field (client-side validation test).
        $mform->addElement('hidden', 'id', 'Id', 'size="50"');
        $mform->addRule('id', null, 'required', null, 'client');
        $mform->setType('id', PARAM_TEXT);

        $mform->addElement('text', 'recordingname', get_string('recording_table_header', 'mod_gmeet'), 'size="50"');
        $mform->addRule('recordingname', null, 'required', null, 'client');
        $mform->setType('recordingname', PARAM_TEXT);

        $mform->addElement('textarea', 'recordingdescription', get_string('desc_table_header', 'mod_gmeet'), 'size="50"');
        $mform->addRule('recordingdescription', null, 'required', null, 'client');
        $mform->setType('recordingdescription', PARAM_TEXT);
     }

    protected function get_page_url_for_dynamic_submission(): \moodle_url {
        return new \moodle_url('/mod/gmeet/oauth2callback.php');
    }
}
