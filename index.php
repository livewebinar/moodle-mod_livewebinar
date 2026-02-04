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

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course);

$context = context_course::instance($course->id);
$event = \mod_livewebinar\event\course_module_instance_list_viewed::create([
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->trigger();

$PAGE->set_url('/mod/livewebinar/index.php', ['id' => $course->id]);
$PAGE->set_title($course->shortname . ': ' . get_string('modulenameplural', 'mod_livewebinar'));
$PAGE->set_heading($course->fullname);

$instances = get_all_instances_in_course('livewebinar', $course);
if (!$instances) {
    notice(
        get_string('thereareno', 'moodle', get_string('modulenameplural', 'mod_livewebinar')),
        new moodle_url('/course/view.php', ['id' => $course->id])
    );
}

$usesections = course_format_uses_sections($course->format);
$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';
if ($usesections) {
    $table->head = [
        get_string('sectionname', 'format_' . $course->format),
        get_string('name'),
    ];
    $table->align = ['center', 'left'];
} else {
    $table->head = [get_string('name')];
    $table->align = ['left'];
}

foreach ($instances as $instance) {
    $link = html_writer::link(
        new moodle_url('/mod/livewebinar/view.php', ['id' => $instance->coursemodule]),
        format_string($instance->name, true)
    );
    if (!$instance->visible) {
        $link = html_writer::tag('span', $link, ['class' => 'dimmed']);
    }

    if ($usesections) {
        $sectionname = get_section_name($course, $instance->section);
        $table->data[] = [$sectionname, $link];
    } else {
        $table->data[] = [$link];
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_livewebinar'), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();
