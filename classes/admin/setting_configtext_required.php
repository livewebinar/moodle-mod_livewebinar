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
 * Text setting that requires non-empty value and validates LiveWebinar credentials.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_configtext_required extends \admin_setting_configtext {
    /** @var array<string, string> temporarily stored submitted values across settings writes. */
    protected static array $pendingvalues = [];

    /**
     * Validate the data before storing it.
     *
     * @param string $data
     * @return bool|string
     */
    public function validate($data) {
        $trimmed = trim((string)$data);
        if ($trimmed === '') {
            return get_string('setting_required', 'mod_livewebinar');
        }
        return parent::validate($trimmed);
    }

    /**
     * Return sanitised value ready to be stored in config table.
     *
     * @param mixed $data
     * @return string
     */
    public function write_setting($data) {
        $data = trim((string)$data);
        $shortname = $this->get_short_name();
        self::$pendingvalues[$shortname] = $data;

        $config = $this->build_config_snapshot();
        $error = $this->validate_credentials($config);
        if ($error !== '') {
            return $error;
        }

        return parent::write_setting($data);
    }

    /**
     * Build a config snapshot including submitted values awaiting persistence.
     *
     * @return \stdClass
     */
    protected function build_config_snapshot(): \stdClass {
        $config = get_config('mod_livewebinar');
        if (!$config) {
            $config = new \stdClass();
        }
        foreach (self::$pendingvalues as $name => $value) {
            $config->{$name} = $value;
        }
        self::$pendingvalues = [];
        return $config;
    }

    /**
     * Validate that configured credentials allow acquiring an access token.
     *
     * @param \stdClass $config
     * @return string Empty string on success, otherwise error message.
     */
    protected function validate_credentials(\stdClass $config): string {
        if (empty($config->identifier) || empty($config->client_id) || empty($config->client_secret)) {
            // Not all values provided yet.
            return '';
        }

        $service = new \mod_livewebinar_client();
        try {
            $service->access_token($config, false, true);
        } catch (\moodle_exception $e) {
            $message = $service->lasterror ?: $e->getMessage();
            $trimmed = trim((string)$message);
            if ($trimmed === '' || preg_match('/^error\\/?$/i', $trimmed)) {
                $message = get_string('credentials_invalid_generic', 'mod_livewebinar');
            }
            return get_string('credentials_invalid', 'mod_livewebinar', $message);
        }

        return '';
    }

    /**
     * Get the short name for this setting (without component prefix).
     *
     * @return string
     */
    protected function get_short_name(): string {
        if (strpos($this->name, '/') !== false) {
            [, $shortname] = explode('/', $this->name, 2);
            return $shortname;
        }
        return $this->name;
    }
}
