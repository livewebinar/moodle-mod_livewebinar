<?php

require_once("../../config.php");
require_once($CFG->libdir . '/adminlib.php');
require_once("lib.php");
require_once(dirname(__FILE__) . '/mod_form.php');

admin_externalpage_setup('managemodules'); // this is hacky, tehre should be a special hidden page for it

$page = optional_param('page', 1, PARAM_INTEGER);

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
$PAGE->set_title(get_string("users", "mod_livewebinar"));

$strmodulename = get_string("modulename", "mod_livewebinar");

echo $OUTPUT->header();
echo $OUTPUT->heading($strmodulename . ': ' . get_string("users", "mod_livewebinar"));

$recordsperpage = 100;
//$extraselect = 'id IN (SELECT distinct userid FROM {role_assignments} a WHERE a.roleid < :ex_courserole1_roleid)';
//$extraparams = array('ex_courserole1_roleid'=>5);
$extraselect = "id IN (SELECT distinct userid FROM mdl_role_assignments a, mdl_role r WHERE a.roleid = r.id AND r.archetype in ('manager','coursecreator','editingteacher','teacher'))";
$extraparams = array();

$userCount = get_users(false, "", false, [], 'email ASC', '', '', $page - 1, $recordsperpage, 'id,email,lastname,firstname',$extraselect,$extraparams);
$pageCount = (int) ($userCount / $recordsperpage);



$users = get_users(true, "", false, [], 'email ASC', '', '', $page - 1, $recordsperpage, 'id,email,lastname,firstname',$extraselect,$extraparams);
$str = '<div class="no-overflow"><table class="admintable generaltable" id="livewebinarusers">';
foreach ($users as $user) {
    $eicon = "<a title=\"" . get_string("edit") . "\" href=\"$CFG->wwwroot/mod/livewebinar/user.php?id={$user->id}&amp;email={$user->email}&amp;sesskey=" . sesskey() . "\">";
    $eicon .= $OUTPUT->pix_icon('t/edit', get_string('edit'));
    $str .= '<tr>';
    $str .= '<td>' . "{$eicon} {$user->lastname} {$user->firstname} ({$user->email})" . '</a></td>';
    $str .= '</tr>';
}
$str .= '</table></div>';
echo $str;

if ($userCount > $recordsperpage) {
    for ($i = 1; $i <= $pageCount; $i++) {
        echo "<a href=\"$CFG->wwwroot/mod/livewebinar/users.php?page={$i}&amp;sesskey=" . sesskey() . "\">{$i}</a>&nbsp;&nbsp;";
    }
}






echo $OUTPUT->footer();
