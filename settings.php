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
 * TODO describe file settings
 *
 * @package    mod_gmeet
 * @copyright  2024 Università degli Studi di Ferrara - Unife
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $options = [''];
    $issuers = \core\oauth2\api::get_all_issuers();

    foreach ($issuers as $issuer) {
        $options[$issuer->get('id')] = s($issuer->get('name'));
    }

    $settings->add(new admin_setting_configselect(
        'gmeet/issuerid',
        get_string('issuerid', 'gmeet'),
        get_string('issuerid_desc', 'gmeet'),
        0,
        $options
    ));

    $settings->add(new admin_setting_configtext(
        'gmeet/domain',
        get_string('domain_setting_name', 'gmeet'),
        get_string('domain_setting_des', 'gmeet'),
        '',
    ));
}
