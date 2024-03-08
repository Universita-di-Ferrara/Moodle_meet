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
 * TODO describe file test
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\oauth2\service\google;

require('../../config.php');
require_login();

$url = new moodle_url('/mod/gmeet/test.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());



$PAGE->set_heading($SITE->fullname);
$PAGE->set_context(context_system::instance());
echo $OUTPUT->header();


$settings = (get_config('gmeet'));

var_dump($settings);

$domain = $settings->domain;
$oauth2 = \core\oauth2\api::get_issuer($settings->issuerid);
var_dump($oauth2);

$clientid = $oauth2->get('clientid');
$clientsecret = $oauth2->get('clientsecret');

echo ("questo è il dominio $domain con i parametri di oauth2, quali clientid: $clientid e con il secret $clientsecret");




echo $OUTPUT->footer();



