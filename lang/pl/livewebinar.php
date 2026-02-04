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
 * Polish strings for livewebinar.
 *
 * @package    mod_livewebinar
 * @copyright  LiveWebinar by RTCLAB Sp. z o.o.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'LiveWebinar Meeting';
$string['modulename'] = 'LiveWebinar Meeting';
$string['modulenameplural'] = 'LiveWebinar Meeting';
$string['pluginadministration'] = 'Zarządzaj LiveWebinar Meeting';
$string['register_txt'] = '';
$string['authorization'] = 'Autoryzacja';
$string['identifier'] = 'Identyfikator';
$string['client_id'] = 'ID klienta API LiveWebinar';
$string['client_id_desc'] = 'Dostępne w panelu użytkownika LiveWebinar';
$string['appdomain'] = 'Domena aplikacji';
$string['appdomain_desc'] = 'Bazowy adres HTTPS dla panelu aplikacji, np. https://app.livewebinar.com';
$string['appdomain_invalid'] = 'Podaj poprawny adres domeny HTTPS (np. https://app.livewebinar.com).';
$string['client_secret'] = 'Klucz prywatny klienta API LiveWebinar';
$string['client_secret_desc'] = 'Dostępne w panelu użytkownika LiveWebinar';
$string['username'] = 'Nazwa użytkownika';
$string['username_desc'] = 'Dostępne w panelu użytkownika LiveWebinar';
$string['password'] = 'Hasło';
$string['password_desc'] = 'Dostępne w panelu użytkownika LiveWebinar';
$string['errorapinotconfigured'] = 'Dane dostępowe API LiveWebinar nie zostały skonfigurowane.';
$string['connectionok'] = 'Jesteś połączony';
$string['connectionstatus'] = 'Status połączenia';
$string['credentials_managed_globally'] = 'Dane dostępowe API LiveWebinar są zarządzane w ustawieniach wtyczki.';
$string['credentials_missing'] = 'Skonfiguruj w ustawieniach wtyczki identyfikator, client id oraz client secret LiveWebinar.';
$string['credentials_invalid'] = 'Dane dostępowe LiveWebinar są nieprawidłowe: {$a}';
$string['credentials_invalid_generic'] = 'Nieprawidłowe dane dostępowe.';
$string['identifier_missing'] = 'Identyfikator jest wymagany. Skonfiguruj identyfikator LiveWebinar w ustawieniach wtyczki.';
$string['setting_required'] = 'To pole jest wymagane.';
$string['topic'] = 'Temat';
$string['description'] = 'Opis';
$string['start_time'] = 'Czas Rozpoczęcia';
$string['duration'] = 'Czas Trwania';
$string['timezone'] = 'Strefa Czasowa';
$string['strict_event'] = 'Czasowe Marginesy Spotkania';
$string['strict_event_help'] = 'TESTUJE Nie pozwalaj uczestnikom na dołączanie przed lub po tym wydarzeniu. Marginesy 60 minut przed i 60 minut później będą miały zastosowanie.';
$string['lock_state'] = 'Zaklucz_ zamknij pokoj na stałe';
$string['lock_state_help'] = 'Uczestnicy wejdą do poczekalni.';
$string['not_scheduled_event'] = 'Pokój otwarty na stałe';
$string['not_scheduled_event_help'] = 'Pokój otwarty cały czas';
$string['open'] = 'Pokój otwarty cały czas';
$string['join_meeting'] = 'Dołącz do spotkania';
$string['minutes_to_join'] = 'Możliwość dołączenia za minut';
$string['recordings'] = 'Nagrania';
$string['roomid'] = 'ID pokoju';
$string['app_panel'] = 'Panel aplikacji';
$string['err_password'] = 'Hasło może zawierać tylko następujące znaki: [a-z A-Z 0-9 @ - _ *]. Maksymalnie 10 znaków.';
$string['err_start_time_past'] = 'Data rozpoczęcia spotkania nie może być w przeszłości.';
$string['err_duration_nonpositive'] = 'Ustaw czas trwania';
$string['err_duration_too_long'] = 'Czas trwania nie może przekroczyć 150 godzin.';
$string['updatewidgets'] = 'Zaktualizuj spotkania';
$string['users'] = 'Użytkownicy';
$string['save'] = 'Zapisz';
$string['livewebinar:view'] = 'Obejrzyj Nowe lekcje online';


$string['gen_report'] = 'Generuj raport';
$string['get_report'] = 'Pobierz raport';
$string['report_will_be_emailed'] = 'Raport zostanie wysłany e-mailem, gdy będzie gotowy.';
