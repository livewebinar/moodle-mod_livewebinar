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
 * Library of interface functions and constants for module livewebinar
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 *
 * All the livewebinar specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* Moodle core API */

/**
 * Returns the information on whether the module supports a feature
 *
 * See {@see plugin_supports()} for more info.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function livewebinar_supports($feature) {

    switch ($feature) {
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the livewebinar into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $livewebinar Submitted data from the form in mod_form.php
 * @param mod_livewebinar_mod_form|null $mform The form instance itself (if needed)
 * @return int The id of the newly inserted livewebinar record
 */
function livewebinar_add_instance(stdClass $livewebinar, ?mod_livewebinar_mod_form $mform = null) {
    global $CFG, $DB, $USER;
    require_once($CFG->dirroot . '/mod/livewebinar/classes/client.php');
    $auth = livewebinar_get_auth_item($USER->id);

    if (empty($auth->client_id) || empty($auth->client_secret) || empty($auth->identifier)) {
        livewebinar_print_error(get_string('errorapinotconfigured'));
    }
    $authrecord = new stdClass();
    $authrecord->user_id = $auth->user_id;
    $authrecord->client_id = $auth->client_id;
    $authrecord->client_secret = $auth->client_secret;

    livewebinar_update_auth_item($authrecord);

    // Create widget on LiveWebinar.
    $service = new mod_livewebinar_client();

    try {
        $widgetid = $service->widget_create($auth, $livewebinar, $USER->id);
    } catch (moodle_exception $e) {
        livewebinar_print_error($service->lasterror);
    }

    // Create widget in database.
    $livewebinar->timemodified = time();
    $livewebinar->widget_id = $widgetid;
    $livewebinar->user_id = $USER->id;
    $livewebinar->id = $DB->insert_record('livewebinar', $livewebinar);

    return $livewebinar->id;
}

/**
 * Updates an instance of the livewebinar in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $livewebinar An object from the form in mod_form.php
 * @param mod_livewebinar_mod_form|null $mform The form instance itself (if needed)
 * @return boolean Success/Fail
 */
function livewebinar_update_instance(stdClass $livewebinar, ?mod_livewebinar_mod_form $mform = null) {
    global $CFG, $DB, $USER;
    require_once($CFG->dirroot . '/mod/livewebinar/classes/client.php');
    $livewebinar->id = $livewebinar->instance;

    $auth = livewebinar_get_auth_item($livewebinar->user_id);
    if (empty($auth->client_id) || empty($auth->client_secret) || empty($auth->identifier)) {
        livewebinar_print_error(get_string('errorapinotconfigured'));
    }

    $authrecord = new stdClass();
    $authrecord->user_id = $livewebinar->user_id;
    $authrecord->client_id = $auth->client_id;
    $authrecord->client_secret = $auth->client_secret;

    livewebinar_update_auth_item($authrecord);

    // Update widget on LiveWebinar.
    $service = new mod_livewebinar_client();
    try {
        $service->widget_update($auth, $livewebinar);
    } catch (moodle_exception $e) {
        livewebinar_print_error($service->lasterror);
    }
    // Update widget in database.
    $livewebinar->timemodified = time();
    $result = $DB->update_record('livewebinar', $livewebinar);

    return $result;
}

/**
 * Removes an instance of the livewebinar from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 * @throws moodle_exception if failed to delete and livewebinar
 *         did not issue a not found/expired error
 */
function livewebinar_delete_instance($id) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/livewebinar/classes/client.php');

    if (!$livewebinar = $DB->get_record('livewebinar', ['id' => $id])) {
        return false;
    }

    if ($livewebinar->widget_id) {
        $auth = livewebinar_get_auth_item($livewebinar->user_id);
        $service = new mod_livewebinar_client();
        try {
            $service->widget_delete($auth, $livewebinar->widget_id);
        } catch (moodle_exception $e) {
            livewebinar_print_error($service->lasterror);
        }
    }
    $DB->delete_records('livewebinar', ['id' => $livewebinar->id]);

    return true;
}

/**
 * Get auth settings.
 *
 * @param int $userid
 */
function livewebinar_get_auth_item($userid) {
    global $DB;
    $auth = $DB->get_record('livewebinar_users', ['user_id' => $userid]);
    if (!$auth) {
        $auth = new stdClass();
        $auth->user_id = $userid;
    }
    $config = get_config('mod_livewebinar');
    if (!empty($config->client_id)) {
        $auth->client_id = $config->client_id;
    }
    if (!empty($config->client_secret)) {
        $auth->client_secret = $config->client_secret;
    }
    if (!empty($config->identifier)) {
        $auth->identifier = $config->identifier;
    }
    return $auth;
}

/**
 * Create or update .
 *
 * @param stdClass $auth
 */
function livewebinar_update_auth_item(stdClass $auth) {
    global $DB;

    $authid = $DB->get_field('livewebinar_users', 'id', ['user_id' => $auth->user_id]);

    if ($authid) {
        $auth->id = $authid;
        return $DB->update_record('livewebinar_users', $auth);
    }
    return $DB->insert_record('livewebinar_users', $auth);
}

/**
 * Print a user-friendly error message when a livewebinar API call errors, or fall back to a generic error message.
 *
 * @param string $error Error message (most likely from mod_livewebinar_webservice->lasterror)
 * @param int $fromapirtc Whether the error originated from RTC API calls
 * @param array $csett Optional cURL settings for debugging
 * @return void
 */
function livewebinar_print_error($error, int $fromapirtc = 0, array $csett = []) {
    global $CFG, $COURSE, $OUTPUT, $PAGE;

    $fromrtc = '';
    if ($fromapirtc) {
        $fromrtc = ' Api RTC';
    }

    if (isset($_SERVER['HTTP_REFERER'])) {
        $nexturl = clean_param($_SERVER['HTTP_REFERER'], PARAM_LOCALURL);
    } else {
        $nexturl = course_get_url($COURSE->id);
    }

    $PAGE->set_title(get_string('error'));
    $PAGE->set_heading($COURSE->fullname);
    echo $OUTPUT->header();

    echo $OUTPUT->notification('<strong>' . get_string('error') . $fromrtc . ':</strong> ' . $error, 'notifytiny');
    if ($CFG->debugdeveloper) {
        echo $OUTPUT->notification('<strong>Stack trace:</strong> ' . format_backtrace(), 'notifytiny');
        if (count($csett)) {
            $curlsettings = json_encode($csett, JSON_PRETTY_PRINT);
            if ($curlsettings === false) {
                $curlsettings = '';
            }
            $curlsettings = s($curlsettings);
            echo $OUTPUT->notification('<strong>RTC cURL request:</strong><pre>' . $curlsettings . '</pre>');
        }
    }
    echo $OUTPUT->continue_button($nexturl);
    echo $OUTPUT->footer();
    exit(1);
}
