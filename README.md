![image](https://github.com/Universita-di-Ferrara/Moodle_meet/assets/80053276/9e6921d0-f1ab-4712-848c-9b568f65eb63)

# Moodle_meet #

Modulo di attvità Google meet: creazione di una stanza meet per conferenze.

Tramite il seguente plugin, sarà possibilie creare una nuova attività in un corso, la quale permetterà l'accesso ad una stanza meet.
Oltre al link di accesso al meet, sarà possibile visualizzare tutte le registrazione effettuate per quel determinato spazio (modalità sincrona)

Il plugin ricorre alle Meet API di Google rilasciate in Beta nel 2023 e ufficialmente nei primi mesi del 2024.

**Documentazione ufficiale** delle API: https://developers.google.com/meet/api/guides/overview?hl=it 

## Configurazioni iniziali obbligatorie

Prima di installare il plugin sarà necessario effettuare delle configurazioni iniziali, di seguito l'elenco:

**Su Google Cloud:**

1. Creare un progetto all'interno della Google Cloud Platform, all'interno di esso abilitare le API necessarie quali Google Meet e Google Drive

2. Creare delle nuove credenziali *ID client OAuth 2.0* di tipologia "Applicazione Web".
   Durante la configurazioni di tali credenziali, aggiungere alla sezione *URI di reindirizzamento autorizzati* il seguente URI: 
   **{your_moodle_url}/mod/gmeet/oauth2callback.php**

**Su Moodle**

3. _Amministrazione del sito_ > _ Gestione Autenticazione_ > Abilitare OAuth 2

4. Nelle configurazioni inserire: **Client ID** e **Secret** recuperati nei passaggi precedenti

5. Nella configurazione del Plugin: Specificare il servizio di OAuth2 configurato (Google).
 
## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/mod/gmeet

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.




## License ##

2023 Università degli Studi di Ferrara - Unife

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
