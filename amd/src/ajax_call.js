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
 * TODO describe module ajax_call
 *
 * @module     mod_gmeet/ajax_call
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';


const getRecordingRequest = (recordingid, courseid) => ({
    methodname: 'mod_gmeet_get_recording',
    args: {
        courseid:courseid,
        id:recordingid,
    },
});

const addRecordingRequest = (recording) => ({
    methodname: 'mod_gmeet_add_recording',
    args: {
        fileid:recording.fileid,
        date:recording.date,
        courseid:recording.courseid,
        instanceid:recording.instanceid,
        name:recording.recordingname,
    }
});

const updateRecordingRequest = (recording) => ({
    methodname: 'mod_gmeet_update_recording',
    args: {
        courseid:recording.courseid,
        id:recording.id,
        name:recording.recordingname,
        description:recording.recordingdescription
    }
});

const deleteRecordingRequest = (recordingid, courseid) => ({
    methodname: 'mod_gmeet_delete_recording',
    args: {
        courseid:courseid,
        id:recordingid,
    }
});

export const getRecording = (recordingid, courseid) => {
    const response = fetchMany([
        getRecordingRequest(recordingid, courseid)
    ]);

    return response[0];
};

export const addRecording = (recording) => {
    const response = fetchMany([
        addRecordingRequest(recording)
    ]);

    return response[0];
};

export const updateRecording = (recording) => {
    const response = fetchMany([
        updateRecordingRequest(recording)
    ]);

    return response[0];
};

export const deleteRecording = (recordingid, courseid) => {
    const response = fetchMany([
       deleteRecordingRequest(recordingid, courseid)
    ]);

    return response[0];
};

