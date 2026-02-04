<?php
/**
 * Prints a particular instance of livewebinar.
 *
 * This file is part of the livewebinar plugin for Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/../../lib/moodlelib.php');

[$course, $cm, $livewebinar] = livewebinar_get_instance_setup();
require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$ismanager = has_capability('mod/livewebinar:addinstance', $context);

// Print the page header.
$PAGE->set_url('/mod/livewebinar/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($livewebinar->name));
$PAGE->set_heading(format_string($course->fullname));

$service = new mod_livewebinar_client();
$auth = livewebinar_get_auth_item($livewebinar->user_id);
$widget = $service->widget_get($auth, $livewebinar->widget_id);
$recordinglist = $service->widget_get_recordings($auth, $livewebinar->widget_id);
$userwidgettoken = $service->get_user_widget_token($auth, $widget, $livewebinar->widget_id, $USER->id);

$genreport = optional_param('genreport', 0, PARAM_INT);
$genreporturl = '';
if ($genreport) {
    $genreporturl = $service->get_report_url($auth, $livewebinar->widget_id);
}

$strtime = get_string('start_time', 'mod_livewebinar');
$strpassword = get_string('password', 'mod_livewebinar');
$strjoin = get_string('join_meeting', 'mod_livewebinar');
$strdescr = get_string('description', 'mod_livewebinar');
$strminutestojoin = get_string('minutes_to_join', 'mod_livewebinar');
$open = get_string('open', 'mod_livewebinar');
$recordings = get_string('recordings', 'mod_livewebinar');
$genreportstr = get_string('gen_report', 'mod_livewebinar');
$getreportstr = get_string('get_report', 'mod_livewebinar');

$starttime = userdate($livewebinar->start_time, '%Y-%m-%d %H:%M:%S');

// Output starts here.
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($livewebinar->name), 2);

if ($widget->start_date) {
    $strtimevalue = $starttime;
} else {
    $strtimevalue = $open;
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_view';
$table->align = ['right', 'left'];
$table->width = '100%';
$table->size = ['15%', '80%'];
$table->data[] = [$strdescr . ':', $livewebinar->intro];
$table->data[] = ['Room ID:', $widget->token];
$table->data[] = [$strtime . ':', $strtimevalue];
$table->data[] = [$strpassword . ':', $widget->password];
echo html_writer::table($table);

$admins = get_admins();
$isadmin = false;
foreach ($admins as $admin) {
    if ($USER->id == $admin->id) {
        $isadmin = true;
        break;
    }
}

$nickname = '';
if (empty($USER->firstname) && empty($USER->lastname)) {
    $nickname = $USER->username;
} else {
    $nickname = trim($USER->firstname . ' ' . $USER->lastname);
}

if ($isadmin) {
    $url = $widget->hosted_at->presenter . '?_nickname=' . urlencode($nickname);
} else if ($ismanager) {
    $url = $widget->hosted_at->host . '?_nickname=' . urlencode($nickname);
} else {
    $url = 'https://embed.archiebot.com/widget-login?attendee_email=' . urlencode($USER->email) .
        '&token=' . $widget->token . '&password_token=' . $userwidgettoken->token .
        '&_nickname=' . urlencode($nickname);
}

if ($isadmin || $ismanager || !$widget->start_date) {
    echo '<a href="' . $url . '" target="_blank">' . $strjoin . '</a><br/>';
} else {
    $minutesuntil = ($widget->start_date - time()) / 60;
    if ($minutesuntil < 90) {
        echo '<a href="' . $url . '" target="_blank">' . $strjoin . '</a><br/>';
    } else {
        echo $strminutestojoin . ': ' . floor($minutesuntil);
    }
}

// Report and app panel.
if ($isadmin || $ismanager) {
    // Report.
    if ($genreporturl) {
        echo '<hr><a href="' . $genreporturl . '" target="_blank">' . $getreportstr . '</a><br />';
    } else {
        echo '<hr><a href="' . $PAGE->url . '&genreport=1">' . $genreportstr . '</a><br/>';
    }

    // App panel.
    $token = base64_encode($service->access_token($auth));
    echo '<hr><a href="https://app.html5meeting.com/auth/login/' . $token .
        '" target="_blank">App Panel</a><br/>';
}

// Recording list.
if ($recordinglist) {
    echo '<hr><h4>' . $recordings . '</h4>';
    foreach ($recordinglist as $recording) {
        echo '<br/><a href="' . $recording->url . '" target="_blank">' .
            $recording->name . '</a><br/>';
    }
}

echo '<br/>';

// Finish the page.
echo $OUTPUT->footer();
