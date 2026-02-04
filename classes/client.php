<?php
// phpcs:ignoreFile
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
 * Handles API calls to LiveWebinar REST API.
 *
 * @package   mod_livewebinar
 * @copyright LiveWebinar by RTCLAB Sp. z o.o.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Web service class.
 *
 * @package   mod_livewebinar
 * @copyright LiveWebinar by RTCLAB Sp. z o.o.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class mod_livewebinar_client {

    /**
     * Last error.
     * @var string
     */
    public $lasterror = '';

    /**
     * Last response.
     * @var string
     */
    public $lastresponse = '';

    /**
     * Archibot API URL.
     * @var string
     */
    private string $api_url = "https://api.archiebot.com/api";

    /**
     * Fetch a config value from provided config structure.
     *
     * @param stdClass|array $config
     * @param string $key
     * @return string
     */
    private function get_config_value($config, string $key): string {
        if (is_object($config) && isset($config->{$key})) {
            return (string)$config->{$key};
        }
        if (is_array($config) && isset($config[$key])) {
            return (string)$config[$key];
        }
        return '';
    }

    /**
     * Resolve identifier value for LiveWebinar API.
     *
     * @param stdClass|array $config
     * @return string
     */
    private function resolve_identifier($config): string {
        $identifier = $this->get_config_value($config, 'identifier');
        if (!empty($identifier)) {
            return $identifier;
        }
        $globalconfig = get_config('mod_livewebinar');
        if (!empty($globalconfig->identifier)) {
            return $globalconfig->identifier;
        }
        return '';
    }

    /**
     * Ensure identifier is present and return it.
     *
     * @param stdClass|array $config
     * @param bool $silent Throw exception instead of showing error if true
     * @return string
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function require_identifier($config, bool $silent = false): string {
        $identifier = $this->resolve_identifier($config);
        if (empty($identifier)) {
            $this->lasterror = get_string('errorapinotconfigured');
            if ($silent) {
                throw new moodle_exception($this->lasterror);
            }
            livewebinar_print_error($this->lasterror);
        }
        return $identifier;
    }

    /**
     * Append identifier header to headers array.
     *
     * @param array $headers
     * @param stdClass|array $config
     * @param bool $silent Throw exception instead of showing error if true
     * @return array
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function add_identifier_header(array $headers, $config, bool $silent = false): array {
        $identifier = $this->require_identifier($config, $silent);
        $headers[] = "Identifier: {$identifier}";
        return $headers;
    }

    /**
     * Build cache key used for storing access tokens.
     *
     * @param stdClass|array $config
     * @return string
     */
    private function get_access_token_cache_key($config): string {
        $identifier = $this->resolve_identifier($config);
        $clientid = $this->get_config_value($config, 'client_id');
        return sha1($clientid . ':' . $identifier);
    }

    /**
     * Get an access token for API calls.
     *
     * @param stdClass|array $config
     * @param bool $fromCache
     * @param bool $silent
     * @return string
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function access_token($config, $fromCache = true, $silent = false) {

        $clientid = $this->get_config_value($config, 'client_id');
        $clientsecret = $this->get_config_value($config, 'client_secret');
        $identifier = $this->require_identifier($config, $silent);

        $cache = cache::make('mod_livewebinar', 'access_token');
        $cachekey = $this->get_access_token_cache_key($config);
        if ($fromCache && ($access_token = $cache->get($cachekey))) {
            return $access_token;
        }

        $curl = curl_init();

        $csett = array(
                CURLOPT_URL => $this->api_url."/auth/login",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => array(
                    'client_id' => $clientid,
                    'client_secret' => $clientsecret,
                    'identifier' => $identifier,
                ),
                CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                    "Accept: application/vnd.archiebot.v1+json"
                ), $config, $silent),
                );
        curl_setopt_array($curl, $csett); 

        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            if ($silent) {
                throw new moodle_exception($err);
            }
            livewebinar_print_error($err);
        } else {

            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                if ($silent) {
                    throw new moodle_exception($response->error->message);
                }
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $this->lastresponse = $response;

            $cache->set($cachekey, $response->token);
            return $response->token;
        }
    }

    public function widget_create($config, $widget, $user_id) {
        $token = $this->access_token($config);
        $curl = curl_init();

        if (!$widget->not_scheduled_event) {
            $start_time = userdate($widget->start_time, '%Y-%m-%d %H:%M:%S');
            $duration = $widget->duration / 60;
        } else {
            $start_time = $duration = "";
        }
        if ($widget->lock_state) {
            $lock_state = "locked";
        } else {
            $lock_state = "unlocked";
        }

        $csett = array(
                CURLOPT_URL => $this->api_url."/widgets",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"name\"\r\n\r\n{$widget->name}\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"password\"\r\n\r\n{$widget->password}\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"agenda\"\r\n\r\n".$this->prepare_agenda($widget->intro)."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"custom_name;\"\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"status\"\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"start_date\"\r\n\r\n{$start_time}\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"duration\"\r\n\r\n{$duration}\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"timezone\"\r\n\r\n{$widget->timezone}\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"strict_event\"\r\n\r\n{$widget->strict_event}\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"manual_confirm_registrants\"\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"autostart[lock_state]\"\r\n\r\n{$lock_state}\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
                CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                    "Accept: application/vnd.archiebot.v1+json",
                    "Authorization: Bearer {$token}",
                    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
                    ), $config),
                );
        curl_setopt_array($curl, $csett); 


        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {

            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $this->lastresponse = $response;

            $cache = cache::make('mod_livewebinar', 'widget');
            $cache->set($response->data->id, $response->data);
            $this->generate_widget_token($config, $widget, $response->data->id, $user_id);

            return $response->data->id;
        }
    }

    public function widget_update($config, $widget) {
        if (!$widget->not_scheduled_event) {
            $start_time = userdate($widget->start_time, '%Y-%m-%d %H:%M:%S');
            $duration = $widget->duration / 60;
        } else {
            $start_time = $duration = "";
        }

        if ($widget->lock_state) {
            $lock_state = "locked";
        } else {
            $lock_state = "unlocked";
        }

        $token = $this->access_token($config);
        $curl = curl_init();

        $csett = array(
                CURLOPT_URL => $this->api_url."/widgets/" . $widget->widget_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS => "name={$widget->name}&password={$widget->password}&start_date={$start_time}&agenda=".$this->prepare_agenda($widget->intro)."&duration={$duration}&timezone={$widget->timezone}&strict_event={$widget->strict_event}&autostart[lock_state]=$lock_state",
                CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                    "Authorization: Bearer {$token}",
                    "Accept: application/vnd.archiebot.v1+json",
                    "Content-Type: application/x-www-form-urlencoded"
                    ), $config),
                );
        curl_setopt_array($curl, $csett); 

        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {

            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $this->lastresponse = $response;

            $cache = cache::make('mod_livewebinar', 'widget');
            $cache->set($response->data->id, $response->data);

            return $response->data->id;
        }
    }

    public function generate_widget_token($config, $widget, $widget_id, $user_id) {
        $token = $this->access_token($config);
        $curl = curl_init();

        $csett = array(
            CURLOPT_URL => $this->api_url."/account/widget_tokens",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "name={$widget->name}&widgets_ids[0]={$widget_id}&amount=1",
            CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                "Authorization: Bearer {$token}",
                "Accept: application/vnd.archiebot.v1+json"
            ), $config),
        );
        curl_setopt_array($curl, $csett);
        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {
            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }

            $cache = cache::make('mod_livewebinar', 'user_widget_token');
            $cache->set($user_id."_".$widget_id, $response->data->tokens->data[0]);

//            TODO unset widgets and tokens
            $cache = cache::make('mod_livewebinar', 'widget_token');
            $cache->set($widget_id, $response->data);

            return $response->data;
        }
    }

    public function generate_user_widget_token($config, $widgetToken, $widget_id, $user_id) {
        $token = $this->access_token($config);
        $curl = curl_init();
        $csett = array(
            CURLOPT_URL => $this->api_url."/account/widget_tokens/items/generate/".$widgetToken->id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "amount=1",
            CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                "Authorization: Bearer {$token}",
                "Accept: application/vnd.archiebot.v1+json"
            ), $config),
        );
        curl_setopt_array($curl, $csett);
        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {
            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $cache = cache::make('mod_livewebinar', 'user_widget_token');
            $cache->set($user_id."_".$widget_id, $response->data->tokens->data[0]);

            return $response->data->tokens->data[0];
        }
    }

    public function get_user_widget_token($config, $widget, $widget_id, $user_id) {

        $widgetTokenCache = cache::make('mod_livewebinar', 'widget_token');
        $userTokenCache = cache::make('mod_livewebinar', 'user_widget_token');
        
        if (!$userWidgetToken = $userTokenCache->get($user_id."_".$widget_id)) {
            if ((!$widgetToken = $widgetTokenCache->get($widget_id))) {
                $widgetToken = $this->generate_widget_token($config, $widget, $widget_id, $user_id);
            }
            $userWidgetToken = $this->generate_user_widget_token($config, $widgetToken, $widget_id, $user_id);
        }

        return $userWidgetToken;
    }

    public function widget_delete($config, $widget_id) {


        $cache = cache::make('mod_livewebinar', 'widget');
        $cache->delete($widget_id);

        $token = $this->access_token($config);
        $curl = curl_init();

        $csett = array(
                CURLOPT_URL => $this->api_url."/widgets/$widget_id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                    "Authorization: Bearer {$token}",
                    "Accept: application/vnd.archiebot.v1+json",
                    "Content-Type: application/x-www-form-urlencoded"
                    ), $config),
                );
        curl_setopt_array($curl, $csett); 

        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {

            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $this->lastresponse = $response;
            return true;
        }
    }

    /**
     * Get widget details from API.
     *
     * @param stdClass|array $config
     * @param int|string $widget_id
     * @param bool $fromCache
     * @return mixed
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function widget_get($config, $widget_id, $fromCache = true) {


        $cache = cache::make('mod_livewebinar', 'widget');
        if ($fromCache && ($widget = $cache->get($widget_id))) {
            return $widget;
        }

        $token = $this->access_token($config);
        $curl = curl_init();

        $csett = array(
                CURLOPT_URL => $this->api_url."/widgets/$widget_id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                    "Authorization: Bearer {$token}",
                    "Accept: application/vnd.archiebot.v1+json"
                    ), $config),
                );
        curl_setopt_array($curl, $csett); 

        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {

            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $this->lastresponse = $response;
            $cache->set($widget_id, $response->data);
            return $response->data;
        }
    }

    public function widget_get_recordings($config, $widget_id) {

        $cache = cache::make('mod_livewebinar', 'recordings');
        if (($recordings = $cache->get($widget_id))) {
            return $recordings;
        }
        $curl = curl_init();
        $token = $this->access_token($config);
        $csett = array(
                CURLOPT_URL => $this->api_url."/widgets/$widget_id/recordings?limit=100",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                    "Authorization: Bearer {$token}",
                    "Accept: application/vnd.archiebot.v1+json"
                    ), $config),
                );


        curl_setopt_array($curl, $csett); 

        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {

            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $this->lastresponse = $response;

            $cache->set($widget_id, $response->data);

            return $response->data;
        }
    }

    public function get_report_url($config, $widget_id) {
        //$this->get_report($config, $widget_id, true);
        $this->create_report($config, $widget_id);

        for ($i = 0; $i < 12; $i++) {
            sleep(2);
            $data = $this->get_report($config, $widget_id);
            if ($data->is_ready) {
                return $data->url;
            }
        }
    }

    /**
     * Fetch report data for a widget.
     *
     * @param stdClass|array $config
     * @param int|string $widget_id
     * @param bool $del
     * @return mixed
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function get_report($config, $widget_id, $del=false) {
        $delTxt = '';
        if($del) {
            $delTxt = '/true';
        }

        $token = $this->access_token($config);

        $curl = curl_init();

        $csett = array(
                CURLOPT_URL => $this->api_url."/reports/widget/{$widget_id}/event/xls{$delTxt}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                    "Authorization: Bearer {$token}",
                    "Cache-Control: no-cache",
                    "Accept: application/vnd.archiebot.v1+json"
                    ), $config),
                );
        curl_setopt_array($curl, $csett); 

        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {

            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $this->lastresponse = $response;

            return $response->data;
        }
    }

    public function create_report($config, $widget_id) {

        $token = $this->access_token($config);
        $curl = curl_init();

        $csett = array(
                CURLOPT_URL => $this->api_url."/reports",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"widget_id\"\r\n\r\n{$widget_id}\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"format\"\r\n\r\nxls\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\n"
                . "Content-Disposition: form-data; name=\"type\"\r\n\r\nevent\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
                CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                    "Authorization: Bearer {$token}",
                    "Accept: application/vnd.archiebot.v1+json",
                    "Cache-Control: no-cache",
                    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
                    ), $config),
                );
        curl_setopt_array($curl, $csett); 

        $jsonresponse = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        } else {

            $response = json_decode($jsonresponse);
            if (isset($response->error)) {
                // Web service error.
                $this->lasterror = $response->error->message;
                livewebinar_print_error($response->error->message,1,$csett);
            }
            $this->lastresponse = $response;
        }
    }

    /**
     * Request autologin token for App Panel.
     *
     * @param stdClass|array $config
     * @param string $appdomain
     * @return string
     */
    public function get_autologin_token($config, string $appdomain): string {
        global $CFG;

        $token = $this->access_token($config);
        $apiurl = rtrim($this->api_url, '/') . '/users/autologinToken';

        $curl = curl_init();
        $csett = array(
            CURLOPT_URL => $apiurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $this->add_identifier_header(array(
                "Authorization: Bearer {$token}",
                "Accept: application/vnd.archiebot.v1+json"
            ), $config),
        );
        curl_setopt_array($curl, $csett);

        $rawresponse = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contenttype = (string)curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $redirecturl = (string)curl_getinfo($curl, CURLINFO_REDIRECT_URL);
        $headersize = (int)curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        curl_close($curl);

        if ($err) {
            $this->lasterror = $err;
            livewebinar_print_error($err);
        }

        $header = '';
        $body = $rawresponse;
        if ($headersize > 0) {
            $header = substr((string)$rawresponse, 0, $headersize);
            $body = substr((string)$rawresponse, $headersize);
        }

        if ($httpcode >= 300 && $httpcode < 400) {
            $location = '';
            if (preg_match('/^Location:\\s*(.+)$/mi', (string)$header, $matches)) {
                $location = trim($matches[1]);
            }
            if ($location !== '') {
                $parsed = parse_url($location);
                if (empty($parsed['scheme'])) {
                    $base = parse_url($apiurl);
                    $prefix = $base['scheme'] . '://' . $base['host'];
                    $location = $prefix . '/' . ltrim($location, '/');
                }
                $curl = curl_init();
                $csett[CURLOPT_URL] = $location;
                $csett[CURLOPT_HEADER] = false;
                curl_setopt_array($curl, $csett);
                $rawresponse = curl_exec($curl);
                $err = curl_error($curl);
                $httpcode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $contenttype = (string)curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
                curl_close($curl);
                if ($err) {
                    $this->lasterror = $err;
                    livewebinar_print_error($err);
                }
                $header = '';
                $body = $rawresponse;
            }
        }

        $response = json_decode((string)$body);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->lasterror = 'Autologin endpoint returned non-JSON response (HTTP ' . $httpcode . ').';
            if ($redirecturl !== '') {
                $this->lasterror .= ' Redirected to: ' . $redirecturl . '.';
            }
            if (!empty($CFG->debugdeveloper)) {
                $snippet = trim(preg_replace('/\\s+/', ' ', (string)$body));
                $snippet = substr($snippet, 0, 200);
                $this->lasterror .= ' Content-Type: ' . $contenttype . '. Response: ' . $snippet;
            }
            livewebinar_print_error($this->lasterror);
        }
        if (isset($response->error)) {
            $this->lasterror = $response->error->message;
            livewebinar_print_error($response->error->message, 1, $csett);
        }

        $token = null;
        if (isset($response->data)) {
            if (is_array($response->data)) {
                if (isset($response->data[0]->autologin_token)) {
                    $token = $response->data[0]->autologin_token;
                } else if (isset($response->data[0]->autologinToken)) {
                    $token = $response->data[0]->autologinToken;
                } else if (isset($response->data[0]['autologin_token'])) {
                    $token = $response->data[0]['autologin_token'];
                } else if (isset($response->data[0]['autologinToken'])) {
                    $token = $response->data[0]['autologinToken'];
                } else if (isset($response->data['autologin_token'])) {
                    $token = $response->data['autologin_token'];
                } else if (isset($response->data['autologinToken'])) {
                    $token = $response->data['autologinToken'];
                }
            } else if (is_object($response->data)) {
                if (isset($response->data->autologin_token)) {
                    $token = $response->data->autologin_token;
                } else if (isset($response->data->autologinToken)) {
                    $token = $response->data->autologinToken;
                }
            }
        } else if (isset($response->autologin_token)) {
            $token = $response->autologin_token;
        } else if (isset($response->autologinToken)) {
            $token = $response->autologinToken;
        }

        if (!empty($token)) {
            return $token;
        }

        if (isset($response->message)) {
            $this->lasterror = (string)$response->message;
        } else {
            $this->lasterror = 'Autologin token missing.';
        }
        livewebinar_print_error($this->lasterror);
    }

    public function prepare_agenda($agenda) {
        $agenda = str_replace('<br>',"\n",$agenda);
        $agenda = str_replace('<br/>',"\n",$agenda);
        $agenda = str_replace('<br />',"\n",$agenda);
        $agenda = strip_tags($agenda);
        return $agenda;
    }

}
