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

use context_system;
use DateTime;
use html_writer;
use table_sql;

/**
 * Class recordings_table
 *
 * @package    mod_gmeet
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recordings_table extends table_sql {

    const GOOGLE_ENDPOINT = 'https://drive.google.com/file/d/{file_id}/view?usp=drive_web';

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid) {
        global $COURSE;
        $context = \context_course::instance($COURSE->id);
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('id', 'name', 'description', 'date', 'file_id');
        // Check if user has the capability to update a course(teacher??).
        if (has_capability('moodle/course:update', $context)) {
            array_push($columns, 'action');
        }
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = [
            'Id',
            get_string('recording_table_header', 'gmeet'),
            get_string('desc_table_header', 'mod_gmeet'),
            get_string('date_table_header', 'mod_gmeet'),
            get_string('link_table_header', 'mod_gmeet'),
        ];
        // Check if user has the capability to update a course(teacher??).
        if (has_capability('moodle/course:update', $context)) {
            array_push($headers, get_string('action_table_header', 'mod_gmeet'),);
        }
        $this->define_headers($headers);
    }

    /**
     * This function is called for each data row to allow processing of the
     * username value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return username with link to profile or username only
     *     when downloading.
     */
    function col_file_id($values) {

        $link = str_replace("{file_id}",$values->file_id,$this::GOOGLE_ENDPOINT);
        // If the data is being downloaded than we don't want to show HTML.
        $button = html_writer::link ($link,get_string('recording_link_button','gmeet'),['class' => 'btn btn-primary']);
        return $button;
        
    }

    function col_date($values) {
        if (isset($values->date)) {
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s',$values->date);
            $date = $datetime->format('d-m-Y');
            return $date;
        } else return NULL;
    }

    function col_action($values) {
        global $OUTPUT, $COURSE;
        $context = \context_course::instance($COURSE->id);
        if (has_capability('moodle/course:update', $context)) {
            // Made an icon nested link for actions.
            $startdiv = html_writer::start_div();
            $icon = $OUTPUT->pix_icon('action','Modifica','mod_gmeet');
            $linkedit = html_writer::link("#",$icon,[
                'data-role'=>'editfield', 
                'data-id' => $values->id,
                'class' => 'mr-3'
            ]);

            $icon = $OUTPUT->pix_icon('trash','Elimina','mod_gmeet');
            $linkdelete = html_writer::link("#",$icon,[
                'data-role'=>'deletefield', 
                'data-id' => $values->id,
                'class' => 'mr-3',
            ]);

            $enddiv = html_writer::end_div();

            $html = $startdiv.$linkedit.$linkdelete.$enddiv;
            return $html;
        } 
        return NULL;
    }

    /**
     * This function is called for each data row to allow processing of
     * columns which do not have a *_cols function.
     * @return string return processed value. Return NULL if no change has
     *     been made.
     */
    function other_cols($colname, $value) {
        
        return NULL;
    }

    function get_sql_sort()
    {
        $sql = 'date DESC';
        return $sql;
    }
}
