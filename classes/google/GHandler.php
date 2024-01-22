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

use DateTime;
use html_writer;
use moodle_url;
use dml_missing_record_exception;
use moodle_exception;
use stdClass;

/**
 * Class GHandler
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class GHandler
{

    private $client = null;
    public $enabled = null;

    private $issuer = null;

    // potrebbe essere inserito nelle impostazioni ?? dovrebbero essere esclusivamente queste
    const SCOPES = 'https://www.googleapis.com/auth/meetings.space.created';

    public function __construct()
    {
        try {
            $this->issuer = \core\oauth2\api::get_issuer(get_config('googlemeet', 'issuerid'));
        } catch (dml_missing_record_exception $e) {
            $this->enabled = false;
        }

        if ($this->issuer && (!$this->issuer->get('enabled') || $this->issuer->get('id') == 0)) {
            $this->enabled = false;
        }

        $client = $this->get_user_oauth_client();
        if ($this->enabled && $client->get_login_url()->get_host() !== 'accounts.google.com') {
            throw new moodle_exception('invalidissuerid', 'googlemeet');
        }
    }
    public function get_user_oauth_client($overrideurl = false)
    {
        if ($this->client) {
            return $this->client;
        }
        if ($overrideurl) {
            $returnurl = $overrideurl;
        } else {
            $returnurl = new moodle_url('/mod/gmeet/oauth2callback.php');
            $returnurl->param('callback', 'yes');
            $returnurl->param('sesskey', sesskey());
        }

        
    }

        /**
     * Checks whether the user is authenticate or not.
     *
     * @return bool true when logged in.
     */
    public function check_login() {
        $client = $this->get_user_oauth_client();
        return $client->is_logged_in();
    }

     /**
     * Print the login in a popup.
     *
     * @param array|null $attr Custom attributes to be applied to popup div.
     */
    public function print_login_popup($attr = null) {
        global $OUTPUT;

        $client = $this->get_user_oauth_client();
        $url = new moodle_url($client->get_login_url());
        $state = $url->get_param('state') . '&reloadparent=true';
        $url->param('state', $state);

        return html_writer::div('
            <button class="btn btn-primary" onClick="javascript:window.open(\''.$client->get_login_url().'\',
                \'Login\',\'height=600,width=599,top=0,left=0,menubar=0,location=0,directories=0,fullscreen=0\'
            ); return false">'.get_string('logintoaccount', 'googlemeet').'</button>', 'mt-2');

    }

        /**
     * Logout.
     *
     * @return string
     */
    public function logout() {
        global $PAGE;

        if ($this->check_login()) {
            $url = new moodle_url($PAGE->url);
            $client = $this->get_user_oauth_client();
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
     * Store the access token.
     */
    public function callback() {
        $client = $this->get_user_oauth_client();
        // This will upgrade to an access token if we have an authorization code and save the access token in the session.
        $client->is_logged_in();
    }

    public function print_user_info($scope = null) {
        global $OUTPUT, $PAGE;

        if (!$this->check_login()) {
            return '';
        }

        $userauth = $this->get_user_oauth_client();
        $userinfo = $userauth->get_userinfo();
        $username = $userinfo['username'];
        $name = $userinfo['firstname'].' '.$userinfo['lastname'];
        $userpicture = base64_encode($userinfo['picture']);

        $userurl = '#';
        if ($scope == 'calendar') {
            $userurl = new moodle_url('https://calendar.google.com/');
        }
        if ($scope == 'drive') {
            $userurl = new moodle_url('https://drive.google.com/');
        }

        $logouturl = new moodle_url($PAGE->url);
        $logouturl->param('logout', true);

        $img = html_writer::img('data:image/jpeg;base64,'.$userpicture, '');
        $out = html_writer::start_div('', ['id' => 'googlemeet_auth-info']);
        $out .= html_writer::link($userurl, $img,
            ['id' => 'googlemeet_picture-user', 'target' => '_blank', 'title' => get_string('manage', 'googlemeet')]
        );
        $out .= html_writer::start_div('', ['id' => 'googlemeet_user-name']);
        $out .= html_writer::span(get_string('loggedinaccount', 'googlemeet'), '');
        $out .= html_writer::span($name);
        $out .= html_writer::span($username);
        $out .= html_writer::end_div();
        $out .= html_writer::link($logouturl,
            $OUTPUT->pix_icon('logout', '', 'googlemeet', ['class' => 'm-0']),
            ['class' => 'btn btn-secondary btn-sm', 'title' => get_string('logout', 'googlemeet')]
        );

        $out .= html_writer::end_div();

        return $out;
    }

    public function get_credentails(){
        $client = $this->get_user_oauth_client();
        return $client->get_raw_userinfo();

    }

}
