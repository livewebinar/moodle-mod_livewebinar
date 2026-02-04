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
 * LiveWebinar users administration page.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// This is hacky; there should be a special hidden page for it.
admin_externalpage_setup('managemodules');

require_once(__DIR__ . '/lib.php');

$page = optional_param('page', 1, PARAM_INT);

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

$url = new moodle_url('/mod/livewebinar/users.php');
$PAGE->set_url($url);
$PAGE->set_title(get_string('users', 'mod_livewebinar'));

$strmodulename = get_string('modulename', 'mod_livewebinar');

echo $OUTPUT->header();
echo $OUTPUT->heading($strmodulename . ': ' . get_string('users', 'mod_livewebinar'));

$recordsperpage = 100;
$extraselect = "id IN (SELECT DISTINCT userid
                  FROM {role_assignments} a
                  JOIN {role} r ON r.id = a.roleid
                 WHERE r.archetype IN ('manager', 'coursecreator', 'editingteacher', 'teacher'))";
$extraparams = [];

$usercount = get_users(
    false,
    '',
    false,
    [],
    'email ASC',
    '',
    '',
    $page - 1,
    $recordsperpage,
    'id,email,lastname,firstname',
    $extraselect,
    $extraparams
);
$pagecount = (int) ($usercount / $recordsperpage);

$users = get_users(
    true,
    '',
    false,
    [],
    'email ASC',
    '',
    '',
    $page - 1,
    $recordsperpage,
    'id,email,lastname,firstname',
    $extraselect,
    $extraparams
);
$str = '<div class="no-overflow"><table class="admintable generaltable" id="livewebinarusers">';
foreach ($users as $user) {
    $eicon = '<a title="' . get_string('edit') . '" href="' . $CFG->wwwroot .
        '/mod/livewebinar/user.php?id=' . $user->id . '&amp;email=' . $user->email .
        '&amp;sesskey=' . sesskey() . '">';
    $eicon .= $OUTPUT->pix_icon('t/edit', get_string('edit'));
    $str .= '<tr>';
    $str .= '<td>' . "{$eicon} {$user->lastname} {$user->firstname} ({$user->email})" .
        '</a></td>';
    $str .= '</tr>';
}
$str .= '</table></div>';
echo $str;

if ($usercount > $recordsperpage) {
    for ($i = 1; $i <= $pagecount; $i++) {
        echo '<a href="' . $CFG->wwwroot . '/mod/livewebinar/users.php?page=' . $i .
            '&amp;sesskey=' . sesskey() . "\">{$i}</a>&nbsp;&nbsp;";
    }
}

echo $OUTPUT->footer();
