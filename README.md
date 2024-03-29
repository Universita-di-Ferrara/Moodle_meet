# Moodle_meet #
<img width="1303" alt="Screenshot 2024-03-20 alle 10 22 16" src="https://github.com/Universita-di-Ferrara/Moodle_meet/assets/80053276/398c3270-9825-4afc-8923-7598cc588be3">


Modulo di attvità Google meet: creazione di una stanza meet per conferenze.

Tramite il seguente plugin, sarà possibilie creare una nuova attività in un corso, la quale permetterà l'accesso ad una stanza meet.
Oltre al link di accesso al meet, sarà possibile visualizzare tutte le registrazione effettuate per quel determinato spazio (modalità sincrona)

Il plugin ricorre alle Meet API di Google rilasciate in Beta nel 2023 e ufficialmente nei primi mesi del 2024.

📚 [Documentazione ufficiale delle API di Google](https://developers.google.com/meet/api/guides/overview?hl=it)

## ⚠️ Configurazioni iniziali obbligatorie

Prima di installare il plugin sarà necessario effettuare delle configurazioni iniziali, di seguito l'elenco:

###Per prima cosa configurare il progetto su Google Cloud: ### 

1. **Creare un progetto** all'interno della [Google Cloud Platform](https://console.cloud.google.com/),

2. Dal Menù API e Servizi **abilitare le API** necessarie: 

   _Google Meet_

   _Google Drive_

4. **Creare delle nuove credenziali** *ID client OAuth 2.0* di tipologia "Applicazione Web".
   
5. Durante la configurazioni di tali credenziali, **aggiungere alla sezione** *URI di reindirizzamento autorizzati* il seguente URI: 
   **{your_moodle_url}/mod/gmeet/oauth2callback.php**

### Su Moodle ### 

1. _Amministrazione del sito_ > _Servizi OAuth 2_ > Abilitare OAuth 2

2. Nelle configurazioni inserire: **Client ID** e **Secret** recuperati nei passaggi precedenti

3. Nella configurazione del Plugin: Specificare il servizio di OAuth2 configurato (Google).
 
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

## Inspired by ##

[Google Meet™ for Moodle](https://github.com/ronefel/moodle-mod_googlemeet) by [Rone Santos](https://github.com/ronefel)

## License ##

2024 Università degli Studi di Ferrara - Unife

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
