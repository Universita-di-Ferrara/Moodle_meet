<?php
// Standard GPL and phpdocs

namespace mod_gmeet\output;

use plugin_renderer_base;


/**
 * TODO describe file renderer
 *
 * @package    mod_gmeet
 * @copyright  2023 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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