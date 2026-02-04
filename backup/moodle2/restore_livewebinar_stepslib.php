<?php
// This file is part of the LiveWebinar plugin for Moodle - http://moodle.org/
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
 * Define all the restore steps that will be used by the restore_livewebinar_activity_task
 *
 * @package   mod_livewebinar
 * @category  backup
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/livewebinar/locallib.php');

/**
 * Structure step to restore one livewebinar activity
 *
 * @package   mod_livewebinar
 * @category  backup
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_livewebinar_activity_structure_step extends restore_activity_structure_step {
    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return restore_path_element[] List of restore path elements
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('livewebinar', '/activity/livewebinar');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_livewebinar($data) {
        global $DB;

        $data = (object) $data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Create the livewebinar instance.
        $newitemid = $DB->insert_record('livewebinar', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add livewebinar related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_livewebinar', 'intro', null);
    }

}
