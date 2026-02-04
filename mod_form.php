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
 * The main livewebinar configuration form.
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/livewebinar/classes/client.php');
require_once($CFG->dirroot . '/mod/livewebinar/lib.php');

/**
 * Module instance settings form
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_livewebinar_mod_form extends moodleform_mod {
    /**
     * Defines forms elements
     */
    public function definition() {
        global $USER;
        // Start of form definition.
        $mform = $this->_form;

        $userid = (isset($this->current) && isset($this->current->user_id) && $this->current->user_id) ?
            $this->current->user_id : $USER->id;
        $admins = get_admins();
        $isadmin = false;
        foreach ($admins as $admin) {
            if ($USER->id == $admin->id) {
                $isadmin = true;
                break;
            }
        }

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Add topic (stored in database as 'name').
        $mform->addElement('text', 'name', get_string('topic', 'livewebinar'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 300), 'maxlength', 300, 'client');

        // Add description ('intro' and 'introformat').
        $this->standard_intro_elements();

        // Add open event.
        $mform->addElement('advcheckbox', 'not_scheduled_event', get_string('not_scheduled_event', 'livewebinar'));
        $mform->setDefault('not_scheduled_event', 0);
        $mform->addHelpButton('not_scheduled_event', 'not_scheduled_event', 'livewebinar');
        // Add date/time. Validation in validation().
        $mform->addElement('date_time_selector', 'start_time', get_string('start_time', 'livewebinar'));
        // Disable for open meetings.
        $mform->disabledIf('start_time', 'not_scheduled_event', 'checked');

        // Add duration.
        $mform->addElement('duration', 'duration', get_string('duration', 'livewebinar'), ['optional' => false]);
        // Validation in validation(). Default to one hour.
        $mform->setDefault('duration', ['number' => 1, 'timeunit' => 3600]);
        $mform->disabledIf('duration', 'not_scheduled_event', 'checked');

        $timezoneidentifiers = timezone_identifiers_list();
        $timezonelist = [];
        foreach ($timezoneidentifiers as $timezone) {
            $timezonelist[$timezone] = $timezone;
        }

        // Adding timezone.
        $mform->addElement('select', 'timezone', get_string('timezone', 'livewebinar'), $timezonelist);
        $mform->setDefault('timezone', date_default_timezone_get());

        // Add strict event.
        $mform->addElement('advcheckbox', 'strict_event', get_string('strict_event', 'livewebinar'));
        $mform->setDefault('strict_event', 0);
        $mform->addHelpButton('strict_event', 'strict_event', 'livewebinar');

        // Add recurring.
        $mform->addElement('advcheckbox', 'lock_state', get_string('lock_state', 'livewebinar'));
        $mform->setDefault('lock_state', 1);
        $mform->addHelpButton('lock_state', 'lock_state', 'livewebinar');

        // Add password.
        $mform->addElement('password', 'password', get_string('password', 'livewebinar'), ['maxlength' => '10']);
        // Check password uses valid characters.
        $regex = '/^[a-zA-Z0-9@_*-]{1,10}$/';
        $mform->addRule('password', get_string('err_password', 'livewebinar'), 'regex', $regex, 'client');

        // Add meeting id.
        $mform->addElement('hidden', 'widget_id', -1);
        $mform->setType('widget_id', PARAM_ALPHANUMEXT);

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();
        // Add standard buttons, common to all modules.
        $mform->addElement('hidden', 'user_id', $userid);
        $mform->setType('user_id', PARAM_INT);
        $mform->addElement('header', 'authorization', get_string('authorization', 'mod_livewebinar'));
        $mform->addElement('static', 'livewebinarcredentialsinfo', '',
            get_string('credentials_managed_globally', 'mod_livewebinar'));
        $this->add_action_buttons();
    }

    /**
     * More validation on form data.
     * See documentation in lib/formslib.php.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = [];

        $service = new mod_livewebinar_client();
        $config = get_config('mod_livewebinar');
        if (empty($config->identifier) || empty($config->client_id) || empty($config->client_secret)) {
            $errors['livewebinarcredentialsinfo'] = get_string('credentials_missing', 'mod_livewebinar');
        } else {
            try {
                $service->access_token($config, false, true);
            } catch (moodle_exception $e) {
                $errors['livewebinarcredentialsinfo'] = get_string('connectionstatus', 'mod_livewebinar') .
                    ': ' . $service->lasterror;
            }
        }
        if (!$data['not_scheduled_event']) {
            // Make sure duration is positive and no more than 150 hours.
            if ($data['duration'] <= 0) {
                $errors['duration'] = get_string('err_duration_nonpositive', 'livewebinar');
            } else if ($data['duration'] > 150 * 60 * 60) {
                $errors['duration'] = get_string('err_duration_too_long', 'livewebinar');
            }
        }

        return $errors;
    }

}
