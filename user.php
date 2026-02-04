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
 * LiveWebinar user settings page.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/user_form.php');

// This is hacky; there should be a special hidden page for it.
admin_externalpage_setup('managemodules');

$isadmin = false;
$admins = get_admins();
foreach ($admins as $admin) {
    if ($USER->id == $admin->id) {
        $isadmin = true;
        break;
    }
}
if (!$isadmin) {
    redirect("$CFG->wwwroot/index.php");
}

$id = required_param('id', PARAM_INT);
$email = optional_param('email', '', PARAM_TEXT);

$url = new moodle_url('/mod/livewebinar/user.php', ['id' => $id]);
$user = $DB->get_record('user', ['id' => $id]);
$PAGE->set_url($url);
$PAGE->set_title(get_string('users', 'mod_livewebinar'));

$strmodulename = get_string('modulename', 'mod_livewebinar');

$dateform = new mod_livewebinar_user_form('user.php?id=' . $id . '&email=' . $email, ['user_id' => $id]);
$formdata = $dateform->get_data();
if ($formdata) {
    livewebinar_update_auth_item($formdata);
    redirect($CFG->wwwroot . '/mod/livewebinar/users.php?sesskey=' . sesskey());
    die;
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strmodulename . ': ' . $email);
    echo $dateform->render();
    echo $OUTPUT->footer();
}
