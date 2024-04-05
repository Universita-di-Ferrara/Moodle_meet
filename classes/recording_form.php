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

    /**
     * Returns context where this form is used
     *
     * This context is validated in {@see \external_api::validate_context()}
     *
     * If context depends on the form data, it is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * Example:
     *     $cmid = $this->optional_param('cmid', 0, PARAM_INT);
     *     return context_module::instance($cmid);
     *
     * @return \context
     */
    protected function get_context_for_dynamic_submission(): \context {
        $courseid = $this->optional_param('courseid', '', PARAM_INT);
        return \context_course::instance($courseid);
    }

    /**
     * Checks if current user has access to this form, otherwise throws exception
     *
     * Sometimes permission check may depend on the action and/or id of the entity.
     * If necessary, form data is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * Example:
     *     require_capability('dosomething', $this->get_context_for_dynamic_submission());
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('mod/gmeet:addinstance', $this->get_context_for_dynamic_submission());
    }

    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $id = $this->optional_param('id', 0, PARAM_INT);
     *     $data = api::get_entity($id); // For example, retrieve a row from the DB.
     *     file_prepare_standard_filemanager($data, ...);
     *     $this->set_data($data);
     */
    public function set_data_for_dynamic_submission(): void {
        $this->set_data([
            'id' => $this->optional_param('id', '', PARAM_INT),
            'courseid' => $this->optional_param('courseid', '', PARAM_INT),
            'recordingname' => $this->optional_param('recordingname', '', PARAM_TEXT),
            'recordingdescription' => $this->optional_param('recordingdescription', '', PARAM_TEXT),
        ] + $this->get_options());
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * This method can return scalar values or arrays that can be json-encoded, they will be passed to the caller JS.
     *
     * Submission data can be accessed as: $this->get_data()
     *
     * Example:
     *     $data = $this->get_data();
     *     file_postupdate_standard_filemanager($data, ....);
     *     api::save_entity($data); // Save into the DB, trigger event, etc.
     *
     * @return mixed
     */
    public function process_dynamic_submission() {
        return $this->get_data();
    }

    /**
     * Return option for the form
     * @return mixed $rv
     */
    protected function get_options(): array {
        $rv = [];
        if (!empty($this->_ajaxformdata['option']) && is_array($this->_ajaxformdata['option'])) {
            foreach (array_values($this->_ajaxformdata['option']) as $idx => $option) {
                $rv["option[$idx]"] = clean_param($option, PARAM_CLEANHTML);
            }
        }
        return $rv;
    }

    /**
     * Defines forms elements
     */
    public function definition() {
        global $COURSE;
        $mform = $this->_form;

        // Required field (client-side validation test).
        $mform->addElement('hidden', 'id', 'Id', 'size="50"');
        $mform->addRule('id', null, 'required', null, 'client');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 'courseid', 'size="50"');
        $mform->addRule('courseid', null, 'required', null, 'client');
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('text', 'recordingname', get_string('recording_table_header', 'mod_gmeet'), 'size="50"');
        $mform->addRule('recordingname', null, 'required', null, 'client');
        $mform->setType('recordingname', PARAM_TEXT);

        $mform->addElement('textarea', 'recordingdescription', get_string('desc_table_header', 'mod_gmeet'), 'size="50"');
        $mform->addRule('recordingdescription', null, 'required', null, 'client');
        $mform->setType('recordingdescription', PARAM_TEXT);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * This is used in the form elements sensitive to the page url, such as Atto autosave in 'editor'
     *
     * If the form has arguments (such as 'id' of the element being edited), the URL should
     * also have respective argument.
     *
     * Example:
     *     $id = $this->optional_param('id', 0, PARAM_INT);
     *     return new moodle_url('/my/page/where/form/is/used.php', ['id' => $id]);
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): \moodle_url {
        return new \moodle_url('/mod/gmeet/oauth2callback.php');
    }
}
