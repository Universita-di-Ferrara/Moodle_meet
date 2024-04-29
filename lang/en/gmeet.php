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
 * Plugin strings are defined here.
 *
 * @package     mod_gmeet
 * @category    string
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Moodle Meet';
$string['modulename'] = 'Google Meet';
$string['gmeetname'] = 'Meeting room name';
$string['modulenameplural'] = 'google_meets';
$string['gmeetname_help'] = 'google_meet_help';
$string['gmeetsettings'] = 'Meeting room settings';
$string['gmeetfieldset'] = 'settings fields';
$string['no$gmeetinstances'] = 'No instances of google meet mod';
$string['gmeet:addinstance'] = 'Add an instance of google meet mod';
$string['gmeet:view'] = 'List all instace of gmeet mod';
$string['gmeet:manager'] = 'Manage all moodle meet activities';
$string['pluginadministration'] = 'Google meet administration';
$string['issuerid'] = 'Oauth issuer';
$string['issuerid_desc'] = '<a href="https://github.com/ronefel/moodle-mod_googlemeet/wiki/How-to-create-Client-ID-and-Client-Secret" target="_blank">How to set up an OAuth Service</a>';
$string['domain_setting_name'] = "Google domain which it 's shared recordings with";
$string['domain_setting_des'] = "Please, set your own google domain to share recordings with user inside it";
$string['logintoaccountbutton'] = "Google Login";
$string['logouttoaccountbutton'] = "Google Logout";
$string['logintoyourgoogleaccount'] = 'Log in to your Google account <br> It\'s nedded for syncing the recordings';
$string['loggedinaccount'] = 'Connected Google account';
$string['servicenotenabled'] = 'This service is not enabled';
$string['sessionexpired'] = 'This session is expired, please login again';
$string['recording_link_button'] = 'Open Recording';
$string['editingfield'] = 'Recording \'s edit form';
$string['addrecording_modal'] = 'New recording \'s add form';
$string['synctoast'] = "Retrieving last recordings ...";
$string['edittoast'] = "Saving changes...";
$string['deletetoast'] = "Deleting recording...";
$string['join_meet_button'] = "Join Meet";
$string['sync_recording_button'] = "Sync last recordings";
$string['add_recording_button'] = "Add one recording";
$string['join_meet_info'] = "Click the button below to join the meeting room";
$string['recording_table_header'] = "Recording's name";
$string['recordingurl_table_header'] = "Recording's url from Drive";
$string['recordingfileid_table_header'] = "Recording's Drive file ID";
$string['desc_table_header'] = "Recording's description";
$string['date_table_header'] = "Recording's date";
$string['link_table_header'] = "Recording";
$string['action_table_header'] = "Action";
$string['delete_form_title'] = "Delete Recording";
$string['delete_form_body'] = "Are you sure to delete this recording?";
$string['delete_form_button'] = "Delete";
$string['requireddataform'] = "You have to insert at least one of the fields";
$string['invalid_url'] = "The URL field is not a valid url";
$string['noDriveurl'] = "The URL provided is not a Google Drive url or is not a file's url";
$string['noDrivefile'] = "The file which you want to load does not exist, it's trashed or you don't have the right permissions";
$string['addrecording_formbutton'] = "Add new recording";
$string['urlhelp'] = "Please enter the link that you can find in File > Share > Copy Link";
$string['addrecording_toast'] = "Adding new recordings...";
$string['filealreadyexist'] = "The file you are trying to insert already exist";
