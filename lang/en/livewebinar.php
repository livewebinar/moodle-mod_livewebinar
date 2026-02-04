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
$string['livewebinar:addinstance'] = 'Add a LiveWebinar Meeting instance';
$string['cachedef_access_token'] = 'LiveWebinar access token';
$string['cachedef_widget'] = 'LiveWebinar widget';
$string['cachedef_recordings'] = 'LiveWebinar recordings';
$string['cachedef_widget_token'] = 'LiveWebinar widget token';
$string['cachedef_user_widget_token'] = 'LiveWebinar user widget token';
$string['privacy:metadata:livewebinar'] = 'LiveWebinar activity data.';
$string['privacy:metadata:livewebinar:user_id'] = 'User ID who owns the webinar.';
$string['privacy:metadata:livewebinar:course'] = 'Course ID for the webinar.';
$string['privacy:metadata:livewebinar:intro'] = 'Activity description.';
$string['privacy:metadata:livewebinar:introformat'] = 'Format of the description.';
$string['privacy:metadata:livewebinar:widget_id'] = 'LiveWebinar widget ID.';
$string['privacy:metadata:livewebinar:created_at'] = 'Creation time (ISO).';
$string['privacy:metadata:livewebinar:name'] = 'Webinar name.';
$string['privacy:metadata:livewebinar:start_time'] = 'Start time.';
$string['privacy:metadata:livewebinar:timemodified'] = 'Last modified time.';
$string['privacy:metadata:livewebinar:strict_event'] = 'Time restriction setting.';
$string['privacy:metadata:livewebinar:lock_state'] = 'Initial lock state.';
$string['privacy:metadata:livewebinar:not_scheduled_event'] = 'Room open all the time setting.';
$string['privacy:metadata:livewebinar:duration'] = 'Duration.';
$string['privacy:metadata:livewebinar:timezone'] = 'Timezone.';
$string['privacy:metadata:livewebinar:password'] = 'Meeting password.';
$string['privacy:metadata:livewebinar_users'] = 'LiveWebinar user credentials stored by the plugin.';
$string['privacy:metadata:livewebinar_users:user_id'] = 'User ID.';
$string['privacy:metadata:livewebinar_users:client_id'] = 'API client ID.';
$string['privacy:metadata:livewebinar_users:client_secret'] = 'API client secret.';
$string['privacy:metadata:livewebinar_users:username'] = 'API username.';
$string['privacy:metadata:livewebinar_users:password'] = 'API password.';
$string['privacy:metadata:livewebinar_api'] = 'LiveWebinar API sends data to the external service to manage webinars.';
$string['privacy:metadata:livewebinar_api:identifier'] = 'API identifier.';
$string['privacy:metadata:livewebinar_api:client_id'] = 'API client ID.';
$string['privacy:metadata:livewebinar_api:client_secret'] = 'API client secret.';
$string['privacy:metadata:livewebinar_api:widget_id'] = 'Webinar widget ID.';
$string['privacy:metadata:livewebinar_api:name'] = 'Webinar name.';
$string['privacy:metadata:livewebinar_api:password'] = 'Webinar password.';
$string['privacy:metadata:livewebinar_api:agenda'] = 'Webinar agenda/description.';
$string['privacy:metadata:livewebinar_api:start_date'] = 'Webinar start date.';
$string['privacy:metadata:livewebinar_api:duration'] = 'Webinar duration.';
$string['privacy:metadata:livewebinar_api:timezone'] = 'Webinar timezone.';
$string['privacy:metadata:livewebinar_api:strict_event'] = 'Time restriction setting.';
$string['privacy:metadata:livewebinar_api:lock_state'] = 'Lock state setting.';
$string['register_txt'] = '';
$string['authorization'] = 'Authorization';
$string['identifier'] = 'Identifier';
$string['identifier_desc'] = 'API identifier e.g: livewebinar';
$string['appdomain'] = 'App domain';
$string['appdomain_desc'] = 'HTTPS base URL for the App Panel, e.g. https://app.livewebinar.com';
$string['appdomain_invalid'] = 'Enter a valid HTTPS domain URL (e.g. https://app.livewebinar.com).';
$string['client_id'] = 'LiveWebinar API client id';
$string['client_id_desc'] = 'Available in LiveWebinar panel';
$string['client_secret'] = 'LiveWebinar API client secret';
$string['client_secret_desc'] = 'Available in LiveWebinar panel';
$string['username'] = 'Username';
$string['username_desc'] = 'Available in LiveWebinar panel';
$string['password'] = 'Password';
$string['password_desc'] = 'Available in LiveWebinar panel';
$string['errorapinotconfigured'] = 'LiveWebinar API credentials are not configured.';
$string['error_api'] = 'LiveWebinar API error: {$a}';
$string['error_apirtc'] = 'LiveWebinar RTC API error: {$a}';
$string['connectionok'] = 'You are connected';
$string['connectionstatus'] = 'Connection status';
$string['credentials_managed_globally'] = 'LiveWebinar API credentials are managed in the plugin settings.';
$string['credentials_missing'] = 'Configure LiveWebinar identifier, client id and client secret in plugin settings.';
$string['credentials_invalid'] = 'LiveWebinar credentials are invalid: {$a}';
$string['credentials_invalid_generic'] = 'Invalid credentials.';
$string['identifier_missing'] = 'Identifier is required. Configure the LiveWebinar identifier in plugin settings.';
$string['setting_required'] = 'This value is required.';
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
$string['roomid'] = 'Room ID';
$string['app_panel'] = 'App Panel';
$string['err_password'] = 'Password may only contain the following characters: [a-z A-Z 0-9 @ - _ *]. Max of 10 characters.';
$string['err_start_time_past'] = 'The start date cannot be in the past.';
$string['err_duration_nonpositive'] = 'Set the duration';
$string['err_duration_too_long'] = 'The duration cannot exceed 150 hours.';
$string['updatewidgets'] = 'Update meetings';
$string['users'] = 'Users';
$string['save'] = 'Save';
$string['livewebinar:view'] = 'View Nowa lekcja online meetings';


$string['gen_report'] = 'Generate report';
$string['get_report'] = 'Get report';
$string['report_will_be_emailed'] = 'Report will be emailed to you when it is ready.';
