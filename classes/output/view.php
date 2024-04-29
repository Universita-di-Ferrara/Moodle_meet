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
 * TODO describe file main_page
 *
 * @package    mod_gmeet
 * @copyright  2024 Università degli Studi di Ferrara - Unife
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_gmeet\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * View class for rendering mustache templates view and manage parameters to pass.
 *
 * @package     mod_gmeet
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 */
class view implements renderable, templatable {

    /** @var array $meetinfo  meet info about meeting code and recordings. */
    private $meetinfo = null;

    /**
     * Construct
     *
     * @param array $meetresponse all data about meet gmeet and gmeet_recordings table for rendering in template
     */
    public function __construct($meetresponse) {
        $this->meetinfo = $meetresponse;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     *
     * @return stdClass $meet
     */
    public function export_for_template(renderer_base $output): stdClass {
        $meet = new stdClass();
        $meet->instanceId = $this->meetinfo['instance_id'];
        $meet->courseId = $this->meetinfo['course_id'];
        $meet->spaceName = $this->meetinfo['space_name'];
        $meet->meetingUrl = $this->meetinfo['meeting_url'];
        $meet->isowner = $this->meetinfo['isowner'];
        $meet->ismodmanager = $this->meetinfo['ismodmanager'];
        return $meet;
    }
}
