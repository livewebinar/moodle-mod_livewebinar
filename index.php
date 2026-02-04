<?php
// This file is part of Moodle - http://moodle.org/
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
 * LiveWebinar index page.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_login();

$id = optional_param('id', 0, PARAM_INT);

if ($id) {
    redirect(new moodle_url('/mod/livewebinar/view.php', ['id' => $id]));
}

$PAGE->set_url('/mod/livewebinar/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'mod_livewebinar'));
$PAGE->set_heading(get_string('pluginname', 'mod_livewebinar'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'mod_livewebinar'));
echo $OUTPUT->footer();
