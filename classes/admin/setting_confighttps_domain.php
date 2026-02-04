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

namespace mod_livewebinar\admin;

/**
 * Config setting for HTTPS domain URL.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_confighttps_domain extends \admin_setting_configtext {
    /**
     * Validate the domain value.
     *
     * @param string $data
     * @return bool|string
     */
    public function validate($data) {
        $value = trim((string)$data);
        if ($value === '') {
            return get_string('setting_required', 'mod_livewebinar');
        }

        if (strpos($value, 'https://') !== 0) {
            return get_string('appdomain_invalid', 'mod_livewebinar');
        }

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return get_string('appdomain_invalid', 'mod_livewebinar');
        }

        $parts = parse_url($value);
        if (empty($parts['scheme']) || strtolower($parts['scheme']) !== 'https' || empty($parts['host'])) {
            return get_string('appdomain_invalid', 'mod_livewebinar');
        }
        if (!empty($parts['query']) || !empty($parts['fragment'])) {
            return get_string('appdomain_invalid', 'mod_livewebinar');
        }
        if (!empty($parts['path']) && $parts['path'] !== '/') {
            return get_string('appdomain_invalid', 'mod_livewebinar');
        }

        return parent::validate($value);
    }

    /**
     * Normalize and store the setting.
     *
     * @param string $data
     * @return string
     */
    public function write_setting($data) {
        $value = trim((string)$data);
        $value = rtrim($value, '/');
        return parent::write_setting($value);
    }
}
