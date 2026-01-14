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
 * English strings for livewebinar.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'LiveWebinar Meeting';
$string['modulename'] = 'LiveWebinar Meeting';
$string['modulenameplural'] = 'LiveWebinar Meeting';
$string['pluginadministration'] = 'Manage LiveWebinar Meeting';
//
$string['register_txt'] = '';
$string['authorization'] = 'Authorization';
$string['identifier'] = 'Identifier';
$string['identifier_desc'] = 'API identifier e.g: livewebinar';
$string['client_id'] = 'LiveWebinar API client id';
$string['client_id_desc'] = 'Available in LiveWebinar panel';
$string['client_secret'] = 'LiveWebinar API client secret';
$string['client_secret_desc'] = 'Available in LiveWebinar panel';
$string['username'] = 'Username';
$string['username_desc'] = 'Available in LiveWebinar panel';
$string['password'] = 'Password';
$string['password_desc'] = 'Available in LiveWebinar panel';
$string['errorapinotconfigured'] = 'LiveWebinar API credentials are not configured.';
//
$string['connectionok'] = 'You are connected';
$string['connectionstatus'] = 'Connection status';
$string['credentials_managed_globally'] = 'LiveWebinar API credentials are managed in the plugin settings.';
$string['credentials_missing'] = 'Configure LiveWebinar identifier, client id and client secret in plugin settings.';
$string['credentials_invalid'] = 'LiveWebinar credentials are invalid: {$a}';
$string['identifier_missing'] = 'Identifier is required. Configure the LiveWebinar identifier in plugin settings.';
$string['setting_required'] = 'This value is required.';
//
$string['topic'] = 'Topic';
$string['description'] = 'Description';
$string['start_time'] = 'Start Time';
$string['duration'] = 'Duration';
$string['timezone'] = 'Timezone';
$string['strict_event'] = 'Time Restriction';
$string['strict_event_help'] = 'Do not allow attendees to join before or after this event. Margins of 60 minutes before and 60 minutes after will apply.';
$string['lock_state'] = 'Lock room';
$string['lock_state_help'] = 'The participants will enter waiting room.';
$string['not_scheduled_event'] = 'Permanently open room';
$string['not_scheduled_event_help'] = 'Room open all the time';
$string['open'] = 'Room open all the time';
$string['join_meeting'] = 'Join Meeting';
$string['minutes_to_join'] = 'You can join in minutes';
$string['recordings'] = 'Recordings';
//
$string['err_password'] = 'Password may only contain the following characters: [a-z A-Z 0-9 @ - _ *]. Max of 10 characters.';
$string['err_start_time_past'] = 'The start date cannot be in the past.';
$string['err_duration_nonpositive'] = 'Set the duration';
$string['err_duration_too_long'] = 'The duration cannot exceed 150 hours.';
//
$string['updatewidgets'] = 'Update meetings';
//
$string['users'] = 'Users';
$string['save'] = 'Save';
$string['livewebinar:view'] = 'View Nowa lekcja online meetings';


$string['gen_report'] = 'Generate report';
$string['get_report'] = 'Get report';
