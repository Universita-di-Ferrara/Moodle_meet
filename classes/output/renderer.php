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
 * TODO describe file renderer
 *
 * @package    mod_gmeet
 * @copyright  2024 Università degli Studi di Ferrara - Unife
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace mod_gmeet\output;

use plugin_renderer_base;
/**
 * Render class for rendering mustache templates.
 *
 * @package     mod_gmeet
 * @copyright   2024 Università degli Studi di Ferrara - Unife
 */
class renderer extends plugin_renderer_base {
    /**
     * Defer to template.
     *
     * @param view $page
     *
     * @return string html for the page
     */
    public function render_view($page): string {
        $meet = $page->export_for_template($this);
        return parent::render_from_template('mod_gmeet/view', $meet);
    }
}
