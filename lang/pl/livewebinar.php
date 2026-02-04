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
$string['livewebinar:addinstance'] = 'Dodaj instancję spotkania LiveWebinar';
$string['cachedef_access_token'] = 'Token dostępu LiveWebinar';
$string['cachedef_widget'] = 'Widget LiveWebinar';
$string['cachedef_recordings'] = 'Nagrania LiveWebinar';
$string['cachedef_widget_token'] = 'Token widżetu LiveWebinar';
$string['cachedef_user_widget_token'] = 'Token widżetu użytkownika LiveWebinar';
$string['privacy:metadata:livewebinar'] = 'Dane aktywności LiveWebinar.';
$string['privacy:metadata:livewebinar:user_id'] = 'ID użytkownika będącego właścicielem webinaru.';
$string['privacy:metadata:livewebinar:course'] = 'ID kursu webinaru.';
$string['privacy:metadata:livewebinar:intro'] = 'Opis aktywności.';
$string['privacy:metadata:livewebinar:introformat'] = 'Format opisu.';
$string['privacy:metadata:livewebinar:widget_id'] = 'ID widgetu LiveWebinar.';
$string['privacy:metadata:livewebinar:created_at'] = 'Czas utworzenia (ISO).';
$string['privacy:metadata:livewebinar:name'] = 'Nazwa webinaru.';
$string['privacy:metadata:livewebinar:start_time'] = 'Czas rozpoczęcia.';
$string['privacy:metadata:livewebinar:timemodified'] = 'Czas ostatniej modyfikacji.';
$string['privacy:metadata:livewebinar:strict_event'] = 'Ustawienie ograniczeń czasowych.';
$string['privacy:metadata:livewebinar:lock_state'] = 'Stan blokady pokoju.';
$string['privacy:metadata:livewebinar:not_scheduled_event'] = 'Ustawienie pokoju otwartego cały czas.';
$string['privacy:metadata:livewebinar:duration'] = 'Czas trwania.';
$string['privacy:metadata:livewebinar:timezone'] = 'Strefa czasowa.';
$string['privacy:metadata:livewebinar:password'] = 'Hasło spotkania.';
$string['privacy:metadata:livewebinar_users'] = 'Dane uwierzytelniające LiveWebinar przechowywane przez wtyczkę.';
$string['privacy:metadata:livewebinar_users:user_id'] = 'ID użytkownika.';
$string['privacy:metadata:livewebinar_users:client_id'] = 'ID klienta API.';
$string['privacy:metadata:livewebinar_users:client_secret'] = 'Sekret klienta API.';
$string['privacy:metadata:livewebinar_users:username'] = 'Nazwa użytkownika API.';
$string['privacy:metadata:livewebinar_users:password'] = 'Hasło API.';
$string['privacy:metadata:livewebinar_api'] = 'API LiveWebinar przesyła dane do zewnętrznej usługi w celu obsługi webinarów.';
$string['privacy:metadata:livewebinar_api:identifier'] = 'Identyfikator API.';
$string['privacy:metadata:livewebinar_api:client_id'] = 'ID klienta API.';
$string['privacy:metadata:livewebinar_api:client_secret'] = 'Sekret klienta API.';
$string['privacy:metadata:livewebinar_api:widget_id'] = 'ID widgetu webinaru.';
$string['privacy:metadata:livewebinar_api:name'] = 'Nazwa webinaru.';
$string['privacy:metadata:livewebinar_api:password'] = 'Hasło webinaru.';
$string['privacy:metadata:livewebinar_api:agenda'] = 'Agenda/opis webinaru.';
$string['privacy:metadata:livewebinar_api:start_date'] = 'Data rozpoczęcia webinaru.';
$string['privacy:metadata:livewebinar_api:duration'] = 'Czas trwania webinaru.';
$string['privacy:metadata:livewebinar_api:timezone'] = 'Strefa czasowa webinaru.';
$string['privacy:metadata:livewebinar_api:strict_event'] = 'Ustawienie ograniczeń czasowych.';
$string['privacy:metadata:livewebinar_api:lock_state'] = 'Ustawienie blokady pokoju.';
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
$string['error_api'] = 'Błąd API LiveWebinar: {$a}';
$string['error_apirtc'] = 'Błąd API LiveWebinar RTC: {$a}';
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
