<?php

require_once("../../config.php");
require_once($CFG->libdir . '/adminlib.php');
require_once("lib.php");
require_once(dirname(__FILE__) . '/mod_form.php');


admin_externalpage_setup('managemodules'); // this is hacky, tehre should be a special hidden page for it

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

$url = new moodle_url('/mod/livewebinar/user.php', array('id' => $id));
$user = $DB->get_record('user', array('id' => $id));
$PAGE->set_url($url);
$PAGE->set_title(get_string("users", "mod_livewebinar"));



$strmodulename = get_string("modulename", "mod_livewebinar");

$dateform = new mod_livewebinar_user_form('user.php?id=' . $id.'&email='.$email, ['user_id' => $id]);
$formdata = $dateform->get_data();
if ($formdata) {
    livewebinar_update_auth_item($formdata);
    redirect("$CFG->wwwroot/mod/livewebinar/users.php?sesskey=" . sesskey());
    die;
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strmodulename . ': '.$email);
    echo $dateform->render();
    echo $OUTPUT->footer();
}

