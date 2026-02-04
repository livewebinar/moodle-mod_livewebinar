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
 * Prints a particular instance of livewebinar
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Login check require_login() is called in livewebinar_get_instance_setup();.
// @codingStandardsIgnoreLine
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
//require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/../../lib/moodlelib.php');

$config = get_config('mod_livewebinar');

list($course, $cm, $livewebinar) = livewebinar_get_instance_setup();
//
$context = context_module::instance($cm->id);
$ismanager = has_capability('mod/livewebinar:addinstance', $context);
// Print the page header.
$PAGE->set_url('/mod/livewebinar/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($livewebinar->name));
$PAGE->set_heading(format_string($course->fullname));

//
$service = new mod_livewebinar_client();
$auth = livewebinar_get_auth_item($livewebinar->user_id);
$widget = $service->widget_get($auth, $livewebinar->widget_id);
$recordingList = $service->widget_get_recordings($auth, $livewebinar->widget_id);
$userWidgetToken = $service->get_user_widget_token($auth, $widget, $livewebinar->widget_id, $USER->id);

$genReport = optional_param('genreport', 0, PARAM_INT);
$genReportURL = '';
if($genReport) {
    $genReportURL = $service->get_report_url($auth, $livewebinar->widget_id);
}

$strtime = get_string('start_time', 'mod_livewebinar');
$strpassword = get_string('password', 'mod_livewebinar');
$strjoin = get_string('join_meeting', 'mod_livewebinar');
$strdescr = get_string('description', 'mod_livewebinar');
$strminutestojoin = get_string('minutes_to_join', 'mod_livewebinar');
$open = get_string('open', 'mod_livewebinar');
$recordings = get_string('recordings', 'mod_livewebinar');
$genreport = get_string('gen_report', 'mod_livewebinar');
$getreport = get_string('get_report', 'mod_livewebinar');


$start_time = userdate($livewebinar->start_time, '%Y-%m-%d %H:%M:%S');
//
// Output starts here.
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($livewebinar->name), 2);

if ($widget->start_date) {
    $strtimeval = $start_time;
} else {
    $strtimeval = $open;
}


$table = new html_table();
$table->attributes['class'] = 'generaltable mod_view';
$table->align = array('right', 'left');
$table->width = '100%';
$table->size = array('15%', '80%');
$numcolumns = 2;
$table->data[] = array($strdescr . ':', $livewebinar->intro);
$table->data[] = array('Room ID:', $widget->token);
$table->data[] = array($strtime . ':', $strtimeval);
$table->data[] = array($strpassword . ':', $widget->password);
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
if(empty($USER->firstname) && empty($USER->lastname)) {
    $nickname = $USER->username;
}
else {
    $nickname = trim($USER->firstname.' '.$USER->lastname);
}

if($isadmin) {
    $url = $widget->hosted_at->presenter.'?_nickname='.urlencode($nickname);
}
elseif($ismanager) {
    $url = $widget->hosted_at->host.'?_nickname='.urlencode($nickname);
}
else {
    $url = "https://embed.archiebot.com/widget-login?attendee_email=".urlencode($USER->email)."&token=".$widget->token."&password_token=".$userWidgetToken->token."&_nickname=".urlencode($nickname);
}

if($isadmin || $ismanager || !$widget->start_date) {
    echo "<a href=\"{$url}\" target=\"_blank\">$strjoin</a><br/>";
} else {
    if((($widget->start_date - time()) /60 ) < 90){ // 90 = 30 minutes before event + 60 timezone shift
        echo "<a href=\"{$url}\" target=\"_blank\">$strjoin</a><br/>";
    } else {
        echo $strminutestojoin.": ".floor(($widget->start_date - time()) /60 );
    }
}

//report AND app panel
if($isadmin || $ismanager) {
    //report
    if($genReportURL) {
        echo "<hr><a href=\"{$genReportURL}\" target=\"_blank\">$getreport</a><br />";
    }
    else {
        echo "<hr><a href=\"".$PAGE->__get('url')."&genreport=1\">$genreport</a><br/>";
    }

    //app panel
    $token = base64_encode($service->access_token($auth));
    echo "<hr><a href=\"https://app.html5meeting.com/auth/login/{$token} \" target=\"_blank\">App Panel</a><br/>";
}


//recording list
if ($recordingList) {
    echo "<hr><h4>$recordings</h4>";
    foreach ($recordingList as $recording) {
        echo "<br/><a href=\"{$recording->url} \" target=\"_blank\">{$recording->name}<br/>";
    }
}
echo "<br/>";

// Finish the page.
echo $OUTPUT->footer();
