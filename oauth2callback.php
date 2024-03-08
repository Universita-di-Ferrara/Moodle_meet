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
 * Oauth client instance callback script
 *
 * @package    mod_gmeet
 * @copyright  2024 Università degli Studi di Ferrara - Unife
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


 use mod_gmeet\google\handler;

 require_once(__DIR__ . '/../../config.php');
 require_once(__DIR__ . '/lib.php');
 
 require_login();
 
 // Headers to make it not cacheable.
 header('Cache-Control: no-cache, must-revalidate');
 header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
 
 // Wait as long as it takes for this script to finish.
 core_php_time_limit::raise();
 
 $client = new handler();
 
 // Post callback.
 $client->callback();
 
 // If this request is coming from a popup, close window and reload parent window.
 $js = <<<EOD
 <html>
 <head>
     <script type="text/javascript">
         window.opener.location.reload();
         window.close();
     </script>
 </head>
 <body></body>
 </html>
 EOD;
 die($js);