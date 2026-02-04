<?php
// This file is part of the livewebinar plugin for Moodle - http://moodle.org/
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

namespace mod_livewebinar\task;

/**
 * Scheduled task to sychronize widget data.
 *
 * @package   mod_livewebinar
 * @copyright LiveWebinar by RTCLAB Sp. z o.o.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_widgets extends \core\task\scheduled_task {
    /**
     * Returns name of task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('updatewidgets', 'mod_livewebinar');
    }

    /**
     * Updates widgets that are not expired.
     *
     * @return boolean
     */
    public function execute() {
        global $DB;
        return true;
    }
}
