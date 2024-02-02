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

import { exception as displayException } from 'core/notification';
import Templates from 'core/templates';

/* 
const context = {
    'synced_recordings': ['www.pippotest.com',"www.plutotest.it"]
}; */

const Selectors = {
    actions: {
        syncRecordings: '[data-action="mod_gmeet/sync_recordings"]',
    },
};



const registerEventListeners = (meeting_code) => {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.syncRecordings)) {
            
            // This will call the function to load and render our template.
            Templates.renderForPromise('mod_gmeet/row_recordings_table', context)

                // It returns a promise that needs to be resoved.
                .then(({ html, js }) => {
                    // Here eventually I have my compiled template, and any javascript that it generated.
                    // The templates object has append, prepend and replace functions.
                    Templates.appendNodeContents('.recordings_tbody', html, js);
                })

                // Deal with this exception (Using core/notify exception function is recommended).
                .catch((error) => displayException(error));


        }
    });
};

export const init = (meeting_code) => {
    registerEventListeners(meeting_code);
};