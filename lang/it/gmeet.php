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
 * Strings for component 'gmeet', language 'it'
 *
 * @package    mod_gmeet
 * @category   string
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Moodle Meet';
$string['modulename'] = 'Google Meet';
$string['gmeetname'] = 'Nome della stanza di Google Meet';
$string['modulenameplural'] = 'Google Meets';
$string['gmeetname_help'] = 'Aiuto';
$string['gmeetsettings'] = "Impostazioni dell'attività di google meet";
$string['gmeetfieldset'] = 'Impostazioni dei campi del modulo';
$string['no$gmeetinstances'] = 'Non sono presenti istanze di questa attività';
$string['gmeet:addinstance'] = "Aggiungi una istanza dell'attività di google meet";
$string['gmeet:view'] = 'Visualizza tutte le attività del modulo gmeet (google meet)';
$string['gmeet:manager'] = 'Gestisce tutte le attività del plugin Moodle meet';
$string['pluginadministration'] = 'Pannello di controllo del plugin gmeet (Google Meet)';
$string['issuerid'] = 'Impostazioni del gestore di Oauth';
$string['issuerid_desc'] = '<a href="https://github.com/ronefel/moodle-mod_googlemeet/wiki/How-to-create-Client-ID-and-Client-Secret" target="_blank">Come impostare Oauth Google</a>';
$string['domain_setting_name'] = 'Dominio di condivisione delle registrazioni';
$string['domain_setting_des'] = "Inserire il dominio per il quale si vuole condividere le lezioni, solo gli utenti del dominio possono visualizzare le registrazioni senza richiedere il permesso ";
$string['logintoaccountbutton'] = "Google Login";
$string['loggedinaccount'] = 'Account Google attualmente connesso';
$string['sessionexpired'] = 'La sessione è scaduta, si prega di autenticarsi nuovamente';
$string['logintoyourgoogleaccount'] = 'Clicca il bottone sottostante per autenticarti con Google <br> È necessario per poter sincronizzare le registrazioni';
$string['recording_link_button'] = 'Guarda la registrazione';
$string['synctoast'] = "Sto recuperando le ultime registrazione...";
$string['edittoast'] = "Salvataggio delle modifiche in corso...";
$string['deletetoast'] = "Cancellazione registrazione in corso...";
$string['join_meet_button'] = "Entra nel Meet";
$string['sync_recording_button'] = "Recupera le ultime registrazioni";
$string['add_recording_button'] = "Aggiugi una registrazione";
$string['join_meet_info'] = "Clicca il pulsante sottostante per partecipare alla stanza virtuale";
$string['recording_table_header'] = "Nome della registrazione";
$string['recordingurl_table_header'] = "Url di Drive della registrazione";
$string['recordingfileid_table_header'] = "ID del file su Google Drive della registrazione";
$string['desc_table_header'] = "Descrizione della registrazione";
$string['date_table_header'] = "Data della registrazione";
$string['link_table_header'] = "Registrazione";
$string['action_table_header'] = "Azioni";
$string['editingfield'] = 'Form di modifica della registrazione';
$string['addrecording_modal'] = 'Form di inserimento di una nuova registrazione';
$string['delete_form_title'] = "Cancellazione della registrazione";
$string['delete_form_body'] = "Sei sicuro di cancellare la registrazione corrente?";
$string['delete_form_button'] = "Cancella";
$string['requireddataform'] = "Devi inserire almeno uno dei due campi previsti";
$string['invalid_url'] = "Url dato non è valido";
$string['noDriveurl'] = "L'Url dato non è un url di Google Drive o non è l'URL di un File";
$string['noDrivefile'] = "Il file che vuoi caricare non esiste o è nel cestino o non hai i permessi per accedervi";
$string['addrecording_formbutton'] = "Aggiungi registrazione";
$string['urlhelp'] = "Puoi trovare il link del file al seguente percorso su Drive: Il tuo file > Condividi > Copia Link";
$string['addrecording_toast'] = "Aggiunta nuova registrazione in corso";
$string['filealreadyexist'] = "Il file che stai cercando di inserire è già presente nel database";
