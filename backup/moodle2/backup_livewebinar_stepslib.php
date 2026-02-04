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
 * Defines backup_livewebinar_activity_structure_step class.
 *
 * @package   mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Define the complete livewebinar structure for backup, with file and id annotations.
 *
 * @package   mod_livewebinar
 * @category  backup
 * @copyright LiveWebinar by RTCLAB Sp. z o.o.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @SuppressWarnings(PHPMD.ExcessiveClassName)
 */
class backup_livewebinar_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines the backup structure of the module.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        // Define the root element describing the livewebinar instance.
        $livewebinar = new backup_nested_element(
            'livewebinar',
            ['id'],
            [
                'user_id',
                'course',
                'intro',
                'introformat',
                'widget_id',
                'created_at',
                'name',
                'start_time',
                'timemodified',
                'strict_event',
                'lock_state',
                'not_scheduled_event',
                'duration',
                'timezone',
                'password',
            ]
        );

        // If we had more elements, we would build the tree here.
        // Define data sources.
        $livewebinar->set_source_table('livewebinar', ['id' => backup::VAR_ACTIVITYID]);

        // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.
        // Define file annotations.
        // Intro does not need itemid.
        $livewebinar->annotate_files('mod_livewebinar', 'intro', null);

        // Return the root element (livewebinar), wrapped into standard activity structure.
        return $this->prepare_activity_structure($livewebinar);
    }
}
