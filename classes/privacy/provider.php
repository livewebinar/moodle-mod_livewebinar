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

/**
 * Privacy provider implementation for mod_livewebinar.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_livewebinar\privacy;

use context;
use context_course;
use context_module;
use context_system;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider for mod_livewebinar.
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\core_userlist_provider, \core_privacy\local\request\plugin\provider {
    /**
     * Returns metadata about this plugin's storage.
     *
     * @param collection $collection The collection to add metadata to.
     * @return collection The updated collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('livewebinar', [
            'user_id' => 'privacy:metadata:livewebinar:user_id',
            'course' => 'privacy:metadata:livewebinar:course',
            'intro' => 'privacy:metadata:livewebinar:intro',
            'introformat' => 'privacy:metadata:livewebinar:introformat',
            'widget_id' => 'privacy:metadata:livewebinar:widget_id',
            'created_at' => 'privacy:metadata:livewebinar:created_at',
            'name' => 'privacy:metadata:livewebinar:name',
            'start_time' => 'privacy:metadata:livewebinar:start_time',
            'timemodified' => 'privacy:metadata:livewebinar:timemodified',
            'strict_event' => 'privacy:metadata:livewebinar:strict_event',
            'lock_state' => 'privacy:metadata:livewebinar:lock_state',
            'not_scheduled_event' => 'privacy:metadata:livewebinar:not_scheduled_event',
            'duration' => 'privacy:metadata:livewebinar:duration',
            'timezone' => 'privacy:metadata:livewebinar:timezone',
            'password' => 'privacy:metadata:livewebinar:password',
        ], 'privacy:metadata:livewebinar');

        $collection->add_database_table('livewebinar_users', [
            'user_id' => 'privacy:metadata:livewebinar_users:user_id',
            'client_id' => 'privacy:metadata:livewebinar_users:client_id',
            'client_secret' => 'privacy:metadata:livewebinar_users:client_secret',
            'username' => 'privacy:metadata:livewebinar_users:username',
            'password' => 'privacy:metadata:livewebinar_users:password',
        ], 'privacy:metadata:livewebinar_users');

        $collection->add_external_location_link('livewebinar_api', [
            'identifier' => 'privacy:metadata:livewebinar_api:identifier',
            'client_id' => 'privacy:metadata:livewebinar_api:client_id',
            'client_secret' => 'privacy:metadata:livewebinar_api:client_secret',
            'widget_id' => 'privacy:metadata:livewebinar_api:widget_id',
            'name' => 'privacy:metadata:livewebinar_api:name',
            'password' => 'privacy:metadata:livewebinar_api:password',
            'agenda' => 'privacy:metadata:livewebinar_api:agenda',
            'start_date' => 'privacy:metadata:livewebinar_api:start_date',
            'duration' => 'privacy:metadata:livewebinar_api:duration',
            'timezone' => 'privacy:metadata:livewebinar_api:timezone',
            'strict_event' => 'privacy:metadata:livewebinar_api:strict_event',
            'lock_state' => 'privacy:metadata:livewebinar_api:lock_state',
        ], 'privacy:metadata:livewebinar_api');

        return $collection;
    }

    /**
     * Get contexts for user ID related to this plugin.
     *
     * @param int $userid The user ID.
     * @return contextlist The contextlist.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        global $DB;

        $contextlist = new contextlist();

        if ($DB->record_exists('livewebinar_users', ['user_id' => $userid])) {
            $contextlist->add_system_context();
        }

        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {livewebinar} lw ON lw.id = cm.instance
                 WHERE ctx.contextlevel = :contextlevel
                   AND lw.user_id = :userid";
        $params = [
            'modname' => 'livewebinar',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export user data for approved contexts.
     *
     * @param approved_contextlist $contextlist Approved context list.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel === CONTEXT_SYSTEM) {
                $record = $DB->get_record('livewebinar_users', ['user_id' => $userid]);
                if ($record) {
                    writer::with_context($context)->export_data(
                        [get_string('privacy:metadata:livewebinar_users', 'mod_livewebinar')],
                        $record
                    );
                }
                continue;
            }

            if ($context->contextlevel !== CONTEXT_MODULE) {
                continue;
            }

            $instanceid = self::get_instance_id_from_context($context);
            if (!$instanceid) {
                continue;
            }

            $record = $DB->get_record('livewebinar', ['id' => $instanceid, 'user_id' => $userid]);
            if ($record) {
                writer::with_context($context)->export_data(
                    [get_string('privacy:metadata:livewebinar', 'mod_livewebinar')],
                    $record
                );
            }
        }
    }

    /**
     * Delete all user data in a context.
     *
     * @param context $context The context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if ($context->contextlevel === CONTEXT_SYSTEM) {
            $DB->delete_records('livewebinar_users');
            return;
        }

        if ($context->contextlevel !== CONTEXT_MODULE) {
            return;
        }

        $instanceid = self::get_instance_id_from_context($context);
        if ($instanceid) {
            $DB->set_field('livewebinar', 'user_id', 0, ['id' => $instanceid]);
        }
    }

    /**
     * Delete user data for approved contexts.
     *
     * @param approved_contextlist $contextlist Approved context list.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel === CONTEXT_SYSTEM) {
                $DB->delete_records('livewebinar_users', ['user_id' => $userid]);
                continue;
            }

            if ($context->contextlevel !== CONTEXT_MODULE) {
                continue;
            }

            $instanceid = self::get_instance_id_from_context($context);
            if ($instanceid) {
                $DB->set_field('livewebinar', 'user_id', 0, ['id' => $instanceid, 'user_id' => $userid]);
            }
        }
    }

    /**
     * Add users within a context to the userlist.
     *
     * @param userlist $userlist The userlist.
     */
    public static function get_users_in_context(userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel === CONTEXT_SYSTEM) {
            $userlist->add_from_sql(
                'user_id',
                "SELECT user_id FROM {livewebinar_users}",
                []
            );
            return;
        }

        if ($context->contextlevel !== CONTEXT_MODULE) {
            return;
        }

        $instanceid = self::get_instance_id_from_context($context);
        if ($instanceid) {
            $userlist->add_from_sql(
                'user_id',
                "SELECT user_id FROM {livewebinar} WHERE id = :id",
                ['id' => $instanceid]
            );
        }
    }

    /**
     * Delete user data for a list of users within a context.
     *
     * @param approved_userlist $userlist The approved userlist.
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();
        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        if ($context->contextlevel === CONTEXT_SYSTEM) {
            [$insql, $inparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('livewebinar_users', "user_id $insql", $inparams);
            return;
        }

        if ($context->contextlevel !== CONTEXT_MODULE) {
            return;
        }

        $instanceid = self::get_instance_id_from_context($context);
        if (!$instanceid) {
            return;
        }

        [$insql, $inparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params = array_merge(['id' => $instanceid], $inparams);
        $DB->set_field_select('livewebinar', 'user_id', 0, "id = :id AND user_id $insql", $params);
    }

    /**
     * Resolve livewebinar instance id from a module context.
     *
     * @param context $context
     * @return int|null
     */
    private static function get_instance_id_from_context(context $context): ?int {
        global $DB;

        if ($context->contextlevel !== CONTEXT_MODULE) {
            return null;
        }

        $sql = "SELECT lw.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {livewebinar} lw ON lw.id = cm.instance
                 WHERE cm.id = :cmid";
        $params = [
            'modname' => 'livewebinar',
            'cmid' => $context->instanceid,
        ];
        $record = $DB->get_record_sql($sql, $params);
        return $record ? (int)$record->id : null;
    }
}
