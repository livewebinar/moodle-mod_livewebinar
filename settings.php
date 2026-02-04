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
 * Settings.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/mod/livewebinar/classes/client.php');

    $settings = new admin_settingpage('modsettinglivewebinar', get_string('pluginname', 'mod_livewebinar'));

    // Test connection if it is setup and user is on the settings page.
    if (
        !CLI_SCRIPT
        && $PAGE->url == $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=modsettinglivewebinar'
    ) {
        $status = get_string('connectionok', 'mod_livewebinar');
        $notifyclass = 'notifysuccess';
        $service = new mod_livewebinar_client();
        try {
            $config = get_config('mod_livewebinar');
            $token = $service->access_token($config, false, true);
        } catch (moodle_exception $e) {
            $status = $service->lasterror;
            $notifyclass = 'notifyproblem';
        }
        $statusmessage = $OUTPUT->notification(
            get_string('connectionstatus', 'mod_livewebinar') . ': ' . $status,
            $notifyclass
        );
        $connectionstatus = new admin_setting_heading('mod_livewebinar/connectionstatus', $statusmessage, '');
        $settings->add($connectionstatus);
    }

    $register = new admin_setting_heading('mod_livewebinar/register', '', get_string('register_txt', 'mod_livewebinar'));
    $settings->add($register);

    $identifier = new \mod_livewebinar\admin\setting_configtext_required(
        'mod_livewebinar/identifier',
        get_string('identifier', 'mod_livewebinar'),
        get_string('identifier_desc', 'mod_livewebinar'),
        '',
        PARAM_ALPHANUMEXT
    );
    $settings->add($identifier);

    $clientid = new \mod_livewebinar\admin\setting_configtext_required(
        'mod_livewebinar/client_id',
        get_string('client_id', 'mod_livewebinar'),
        get_string('client_id_desc', 'mod_livewebinar'),
        '',
        PARAM_ALPHANUMEXT
    );
    $settings->add($clientid);

    $clientsecret = new \mod_livewebinar\admin\setting_configtext_required(
        'mod_livewebinar/client_secret',
        get_string('client_secret', 'mod_livewebinar'),
        get_string('client_secret_desc', 'mod_livewebinar'),
        '',
        PARAM_ALPHANUMEXT
    );
    $settings->add($clientsecret);

    $userslabel = get_string('users', 'mod_livewebinar');
    $url = "$CFG->wwwroot/mod/livewebinar/users.php?sesskey=" . sesskey();
    $viconlink = '<a title="' . $userslabel . '" href="' . $url . '">' . $userslabel . '</a>';

    $settings->add(new admin_setting_heading('userConfig', '', $viconlink));
}
