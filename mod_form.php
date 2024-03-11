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
 * The main mod_gmeet configuration form.
 *
 * @package     mod_gmeet
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_gmeet\google\handler;

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_gmeet
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_gmeet_mod_form extends moodleform_mod {
    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        $client = new handler();

        $config = get_config('gmeet');

        $mform = $this->_form;

        $logout = optional_param('logout', 0, PARAM_BOOL);
        if ($logout) {
            $client->logout();
        }

        if (empty($this->current->instance)) {
            $clientislogged = optional_param('client_islogged', false, PARAM_BOOL);

            // Was logged in before submitting the form and the google session expired after submitting the form.
            if ($clientislogged && !$client->check_login()) {

                $mform->addElement('html', html_writer::div(get_string('sessionexpired', 'gmeet') .
                    $client->print_login_popup(), 'mdl-align alert alert-danger googlemeet_loginbutton'
                ));

                // Whether the customer is enabled and if not logged in to the Google account.
            } else if ($client->enabled && !$client->check_login()) {

                $mform->addElement('html', html_writer::div(get_string('logintoyourgoogleaccount', 'gmeet') .
                    $client->print_login_popup(), 'mdl-align alert alert-info googlemeet_loginbutton'
                ));
            }

            // If is logged in, shows Google account information.
            if ($client->check_login()) {
                $mform->addElement('html', html_writer::div($client->print_user_info(), 'mdl-align alert alert-success'));
                $mform->addElement('hidden', 'client_islogged', true);
            }

        } else {
            $mform->addElement('hidden', 'client_islogged', false);
        }

        $mform->setType('client_islogged', PARAM_BOOL);
        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('gmeetname', 'mod_gmeet'), ['size' => '64']);

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'gmeetname', 'mod_gmeet');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }
}
