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
 * Form to search for meeting reports.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/mod/livewebinar/classes/client.php');

/**
 * Meeting report search form.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_livewebinar_user_form extends moodleform {
    /**
     * Define form elements.
     */
    public function definition() {
        $mform = $this->_form;
        $params = $this->_customdata;
        $userid = $params['user_id'];

        $mform->addElement('hidden', 'user_id', $userid);
        $mform->setType('user_id', PARAM_INT);

        $mform->addElement('static', 'livewebinarcredentialsinfo', '',
            get_string('credentials_managed_globally', 'mod_livewebinar'));
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

        return $errors;
    }
}
