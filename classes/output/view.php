<?php
// Standard GPL and phpdocs

namespace mod_gmeet\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;


/**
 * TODO describe file main_page
 *
 * @package    mod_gmeet
 * @copyright  2023 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class view implements renderable, templatable {
    /** @var string $sometext Some text to show how to pass data to a template. */

    private $meet_info = null;

    public function __construct($meet_response){
        $this->meet_info = $meet_response;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $meet = new stdClass();
        $meet->instanceId = $this->meet_info['instance_id'];
        $meet->meetingCode = $this->meet_info['meeting_code'];
        $meet->meetingUrl = $this->meet_info['meeting_url'];
        $meet->meetingRecordings = $this->meet_info['meeting_recordings'];
        return $meet;
    }
}