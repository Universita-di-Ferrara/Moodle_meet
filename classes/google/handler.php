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

namespace mod_gmeet\google;

use html_writer;
use moodle_url;
use dml_missing_record_exception;
use moodle_exception;
use stdClass;
use core\oauth2\api;
use mod_gmeet\rest;
use Throwable;

/**
 * Class handler
 *
 * @package    mod_gmeet
 * @copyright  2024 Università degli Studi di Ferrara - Unife
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler {

    /**
     * Conference setting, Access_Type
     * @var int
     */
    const ACCESS_TYPE_TRUSTED = 2;

    /**
     * Conference setting, Entry_Point
     * @var int
     */
    const ENTRY_POINT_ACCESS_ALL = 1;

    /**
     * Google scopes
     * @var string
     */
    const GOOGLE_SCOPES = 'https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/meetings.space.created';

    /**
     * Conference pageSize limit
     */
    const CONFERENCE_PAGESIZE = 50;

    /**
     * Conference time limit
     */
    const TIME_LIMIT = 15;

    /**
     * OAuth 2 client
     * @var \core\oauth2\client
     */
    private $client = null;

    /**
     * OAuth 2 Issuer
     * @var \core\oauth2\issuer
     */
    private $issuer = null;

    /**
     * Check if client is enabled
     * @var bool
     */
    public $enabled = true;


    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {

        // Check if issuer setting is set in plugin manager.
        try {
            $this->issuer = api::get_issuer(get_config('gmeet', 'issuerid'));
        } catch (dml_missing_record_exception $e) {
            $this->enabled = false;
            // Maybe i cant throw the catched exception ?
        }

        // Check if issuer id disabled or is not Google.

        if ($this->issuer && (!$this->issuer->get('enabled') || !$this->issuer->get('servicetype') == 'google')) {
            $this->enabled = false;
        }

        $this->get_oauth_client();
    }



    /**
     * Get oauth2 client
     *
     * @return \core\oauth2\client
     */
    protected function get_oauth_client() {
        if ($this->client) {
            return $this->client;
        }

        $returnurl = new moodle_url('/mod/gmeet/oauth2callback.php');
        $returnurl->param('callback', 'yes');
        $returnurl->param('sesskey', sesskey());

        $this->client = api::get_user_oauth_client($this->issuer, $returnurl, self::GOOGLE_SCOPES, true);
        return $this->client;
    }

    /**
     * Print the login in a popup.
     *
     * @param array|null $attr Custom attributes to be applied to popup div.
     *
     * @return string HTML code
     */
    public function print_login_popup ($attr = null) {
        global $OUTPUT;

        $client = $this->get_oauth_client();
        $url = new moodle_url($client->get_login_url());
        $state = $url->get_param('state') . '&reloadparent=true';
        $url->param('state', $state);

        return html_writer::div('
            <button class="btn btn-primary" onClick="javascript:window.open(\'' . $client->get_login_url() . '\',
                \'Login\',\'height=600,width=599,top=0,left=0,menubar=0,location=0,directories=0,fullscreen=0\'
            ); return false">' . get_string('logintoaccountbutton', 'gmeet') . '</button>', 'mt-2');
    }


    /**
     * Print the login in a popup.
     *
     * @param array|null $attr Custom attributes to be applied to popup div.
     *
     * @return string HTML code
     */
    public function print_logout_popup ($attr = null) {
        global $PAGE;
        $logouturl = new moodle_url($PAGE->url);
        $logouturl->param('logout', true);

        return html_writer::div('
            <button type="button" class="btn btn-primary" onClick="javascript:window.location.replace(\'' . $logouturl . '\')">' .
            get_string('logouttoaccountbutton', 'gmeet') . '</a>', 'mt-2');
    }

    /**
     * Print user info.
     *
     * @param string|null $scope 'calendar' or 'drive' Defines which link will be used.
     *
     * @return string HTML code
     */
    public function print_user_info ($scope = null) {
        global $PAGE;

        if (!$this->check_login()) {
            return '';
        }
        $userauth = $this->get_oauth_client();
        $userinfo = $userauth->get_userinfo();
        $username = $userinfo['username'];
        $name = $userinfo['firstname'] . ' ' . $userinfo['lastname'];
        $logouturl = new moodle_url($PAGE->url);
        $logouturl->param('logout', true);

        $out = html_writer::start_div('', ['id' => 'googlemeet_user-name', 'style' => 'display:flex; flex-direction:column']);
        $out .= html_writer::span(get_string('loggedinaccount', 'gmeet'), '');
        $out .= html_writer::span($name);
        $out .= html_writer::span($username);
        $out .= $this->print_logout_popup();
        $out .= html_writer::end_div();

        return $out;
    }

    /**
     * Checks whether the user is authenticate or not.
     *
     * @return bool true when logged in.
     */
    public function check_login() {
        $client = $this->get_oauth_client();
        return $client->is_logged_in();
    }

    /**
     * Store the access token.
     *
     * @return void
     */
    public function callback() {
        $client = $this->get_oauth_client();
        // This will upgrade to an access token if we have an authorization code and save the access token in the session.
        $client->is_logged_in();
    }

    /**
     * Logout.
     *
     * @return void
     */
    public function logout() {
        global $PAGE;

        if ($this->check_login()) {
            $url = new moodle_url($PAGE->url);
            $client = $this->get_oauth_client();
            $client->log_out();
            $js = <<<EOD
                <html>
                <head>
                    <script type="text/javascript">
                        window.location = '{$url}'.replaceAll('&amp;','&')
                    </script>
                </head>
                <body></body>
                </html>
            EOD;
            die($js);
        }
    }

    /**
     * Wrapper function to perform an API call and also catch and handle potential exceptions.
     *
     * @param rest $service The rest API object
     * @param string $api The name of the API call
     * @param array $params The parameters required by the API call
     * @param string $rawpost Optional param to include in the body of a post.
     *
     * @return \stdClass The response object
     * @throws moodle_exception
     */
    public static function request($service, $api, $params, $rawpost = false): ?\stdClass {
        try {
            $response = $service->call($api, $params, $rawpost);

        } catch (\Exception $e) {
            if ($e->getCode() == 403 && strpos($e->getMessage(), 'Access Not Configured') !== false) {
                // This is raised when the Drive API service or the Calendar API service
                // has not been enabled on Google APIs control panel.
                throw new moodle_exception('servicenotenabled', 'mod_gmeet');
            }
            throw $e;
        }

        return $response;
    }

    /**
     * Create a new meeting space
     *
     * @return stdClass space
     */
    public function create_space_request() {

        $service = new rest($this->get_oauth_client());
        $args = [];
        $argsraw = [
            "config" => [
                "accessType" => $this::ACCESS_TYPE_TRUSTED,
                "entryPointAccess" => $this::ENTRY_POINT_ACCESS_ALL,
            ],
        ];
        $meetresponse = $this->request($service, 'createspace', $args, json_encode($argsraw));
        return $meetresponse;
    }

    /**
     * List all conference filtered by $meetingcode and start_time >= $timestamp
     *
     * @param string $spacename space name
     * @param string $timestamp timestamp of day from which start filtering
     * @param string $pagetoken nextPageToken if it's not display all results
     *
     * @return stdClass $conferenceresponse all conferences filtered
     */
    public function list_conference_request($spacename, $timestamp, $pagetoken = false) {

        $service = new rest($this->get_oauth_client());
        $argsraw = false;
        $jsonencodedtimestamp = json_encode($timestamp);
        $args = [
            'filter' => "space.name = $spacename start_time >= $jsonencodedtimestamp",
        ];
        $argsraw = [
            'pageSize' => $this::CONFERENCE_PAGESIZE,
        ];
        if ($pagetoken) {
            $argsraw = [
                'pageToken' => $pagetoken,
                'pageSize' => $this::CONFERENCE_PAGESIZE,
            ];
        };
        try {
            $conferenceresponse = $this->request($service, 'listconferences', $args, $argsraw);
            return $conferenceresponse;
        } catch (\Throwable $th) {
            debugging($args);
            debugging($th);
        }
    }
    /**
     * List all recordings filtered by $conferencename
     *
     * @param string $parent parent conference name
     *
     * @return stdClass $recordingsresponse all recordings filtered
     */
    public function list_recordings_request($parent) {

        $service = new rest($this->get_oauth_client());
        $args = [
            'parent' => $parent,
        ];

        $recordingsresponse = $this->request($service, 'listrecordings', $args, false);
        return $recordingsresponse;
    }
    /**
     * Share file with domain
     *
     * @param string $fileid file id to share with domain
     * @param string $domain ou domain
     *
     * @return void
     */
    public function sharefile_request($fileid, $domain) {

        $service = new rest($this->get_oauth_client());
        $args = [
            'fileid' => $fileid,
        ];
        $argsraw = [
            'role' => 'reader',
            'type' => 'domain',
            'domain' => $domain,
        ];

        $response = $this->request($service, 'create_permission', $args, json_encode($argsraw));
        return $response;
    }

    /**
     * Check if the user is the owner of the space
     *
     * @param string $name space code
     *
     * @return bool if the user is the meet owner
     */
    public function getspace_request($name) {

        $service = new rest($this->get_oauth_client());
        $args = [
            'name' => $name,
        ];
        $argsraw = [
            "config" => [
                "accessType" => $this::ACCESS_TYPE_TRUSTED,
                "entryPointAccess" => $this::ENTRY_POINT_ACCESS_ALL,
            ],
        ];

        try {
            $response = $this->request($service, 'get_space', $args, json_encode($argsraw));
            return true;
        } catch (\Throwable $th) {
            debugging($th->getMessage());
            return false;
        }
    }
    /**
     * get the file
     *
     * @param string $fileid fileid
     *
     * @return object $response file
     */
    public function getfile_request($fileid) {

        $service = new rest($this->get_oauth_client());
        $args = [
            'fileid' => $fileid,
            'fields' => 'id, name, trashed, createdTime',
        ];
        try {
            $response = $this->request($service, 'get_file', $args, false);
            return $response;
        } catch (\Throwable $th) {
            debugging($th->getMessage());
            return false;
        }
    }
}
