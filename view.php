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
 * Prints a particular instance of livewebinar.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/../../lib/moodlelib.php');

[$course, $cm, $livewebinar] = livewebinar_get_instance_setup();
require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$ismanager = has_capability('mod/livewebinar:addinstance', $context);
livewebinar_view($livewebinar, $course, $cm, $context);

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
$appautologin = optional_param('appautologin', 0, PARAM_INT);
$reportrequested = false;
if ($genreport) {
    $reportrequested = true;
    if (!isset($SESSION->mod_livewebinar_report_requested)) {
        $SESSION->mod_livewebinar_report_requested = [];
    }
    if (empty($SESSION->mod_livewebinar_report_requested[$livewebinar->id])) {
        $service->create_report($auth, $livewebinar->widget_id);
        $SESSION->mod_livewebinar_report_requested[$livewebinar->id] = time();
    }
}

$config = get_config('mod_livewebinar');
$appdomain = 'https://app.livewebinar.com';
if (!empty($config->appdomain)) {
    $appdomain = $config->appdomain;
}
$appdomain = rtrim($appdomain, '/');

if ($appautologin) {
    $autologintoken = $service->get_autologin_token($auth, $appdomain);
    $redirectto = '/widgets/details/' . $livewebinar->widget_id;
    $autologinurl = $appdomain . '/account_auto_login/' . urlencode($autologintoken) .
        '?redirect_to=' . urlencode($redirectto);
    redirect($autologinurl);
}

$strtime = get_string('start_time', 'mod_livewebinar');
$strpassword = get_string('password', 'mod_livewebinar');
$strjoin = get_string('join_meeting', 'mod_livewebinar');
$strdescr = get_string('description', 'mod_livewebinar');
$strminutestojoin = get_string('minutes_to_join', 'mod_livewebinar');
$open = get_string('open', 'mod_livewebinar');
$recordingslabel = get_string('recordings', 'mod_livewebinar');
$genreportstr = get_string('gen_report', 'mod_livewebinar');
$reportqueuedstr = get_string('report_will_be_emailed', 'mod_livewebinar');
$strroomid = get_string('roomid', 'mod_livewebinar');
$strapppanel = get_string('app_panel', 'mod_livewebinar');

$starttime = userdate($livewebinar->start_time, '%Y-%m-%d %H:%M:%S');

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
$table->data[] = [$strroomid . ':', $widget->token];
$table->data[] = [$strtime . ':', $strtimevalue];
$table->data[] = [$strpassword . ':', $widget->password];

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

$joinurl = null;
$joininfo = null;
if ($isadmin || $ismanager || !$widget->start_date) {
    $joinurl = $url;
} else {
    $minutesuntil = ($widget->start_date - time()) / 60;
    if ($minutesuntil < 90) {
        $joinurl = $url;
    } else {
        $joininfo = $strminutestojoin . ': ' . floor($minutesuntil);
    }
}

$reporturl = null;
$applink = null;
if ($isadmin || $ismanager) {
    $reporturl = new moodle_url($PAGE->url, ['genreport' => 1]);
    $applink = new moodle_url($PAGE->url, ['appautologin' => 1]);
}

$recordingitems = [];
if (!empty($recordinglist)) {
    foreach ($recordinglist as $recording) {
        $recordingitems[] = [
            'url' => $recording->url,
            'name' => $recording->name,
        ];
    }
}

$templatedata = [
    'name' => format_string($livewebinar->name),
    'table_html' => html_writer::table($table),
    'join_url' => $joinurl,
    'join_label' => $strjoin,
    'join_info' => $joininfo,
    'show_report_section' => ($isadmin || $ismanager),
    'report_requested' => $reportrequested,
    'report_text' => $reportqueuedstr,
    'report_url' => $reporturl ? $reporturl->out(false) : '',
    'report_label' => $genreportstr,
    'app_panel_url' => $applink ? $applink->out(false) : '',
    'app_panel_label' => $strapppanel,
    'recordings_label' => $recordingslabel,
    'recordings' => !empty($recordingitems),
    'recording_items' => $recordingitems,
];

// Output starts here.
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_livewebinar/view', $templatedata);
echo $OUTPUT->footer();
