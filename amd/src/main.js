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
 * TODO describe module main
 *
 * @module     mod_gmeet/main
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import ModalForm from 'core_form/modalform';
import {get_string as getString} from 'core/str';
import {getRecording, updateRecording} from './ajax_call';
import {add as addToast} from 'core/toast';

const Selectors = {
    actions: {
        syncRecordings: '[data-action="mod_gmeet/sync_recordings"]',
        editRecording:  '[data-role=editfield][data-id]',
    },
};

const asyncGetRecording = async(id) => {
    const recording = await getRecording(id);
    return recording;
};

const asyncUpdateRecording = async(recording) => {
    const response = await updateRecording(recording);
    return response;
};

const registerEventListeners = () => {
    document.addEventListener('click', e => {
        const editingRecordingelement = e.target.closest(Selectors.actions.editRecording);
        const syncRecordingelement = e.target.closest(Selectors.actions.syncRecordings);
        if (editingRecordingelement) {
            e.preventDefault();
            const recordingid = editingRecordingelement.getAttribute('data-id');
            asyncGetRecording(recordingid).then((values) => {
                const modalForm = new ModalForm({
                    // Name of the class where form is defined (must extend \core_form\dynamic_form):
                    formClass: "mod_gmeet\\recording_form",
                    // Add as many arguments as you need, they will be passed to the form:
                    args: { id: recordingid, recordingname:values.name, recordingdescription:values.description},
                    // Pass any configuration settings to the modal dialogue, for example, the title:
                    modalConfig: {
                        title: getString('editingfield', 'mod_gmeet', editingRecordingelement.getAttribute('data-name'))
                    },
                    // DOM element that should get the focus after the modal dialogue is closed:
                    returnFocus: editingRecordingelement,
                });
                modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (e) => {
                    const formdata = e.detail;
                    addToast(getString('edittoast', 'mod_gmeet'));
                    asyncUpdateRecording(formdata)
                    .then(() => {
                        location.reload();
                    });
                });
                // Show the form.
                modalForm.show();
            });
        }
        if (syncRecordingelement) {
                e.preventDefault();
                addToast(getString('synctoast', 'mod_gmeet'));
                const form = document.getElementById('syncForm');
                form.submit();
                syncRecordingelement.disabled = true;
        }
    });
};



export const init = () => {
    registerEventListeners();
};
