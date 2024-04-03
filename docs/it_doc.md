## ⚠️ Configurazioni iniziali obbligatorie

Prima di installare il plugin sarà necessario effettuare delle configurazioni iniziali, di seguito l'elenco:

### Per prima cosa configurare il progetto su Google Cloud: ### 

1. **Creare un progetto** all'interno della [Google Cloud Platform](https://console.cloud.google.com/),

2. Dal Menù API e Servizi **abilitare le API** necessarie: 

   _Google Meet_

   _Google Drive_

4. **Creare delle nuove credenziali** *ID client OAuth 2.0* di tipologia "Applicazione Web".
   
5. Durante la configurazioni di tali credenziali, **aggiungere alla sezione** *URI di reindirizzamento autorizzati* il seguente URI: 
   **{your_moodle_url}/mod/gmeet/oauth2callback.php**

### Poi su Moodle ### 

1. _Amministrazione del sito_ > _Servizi OAuth 2_ > Abilitare OAuth 2

2. Nelle configurazioni inserire: **Client ID** e **Secret** recuperati nei passaggi precedenti

3. Nella configurazione del Plugin: Specificare il servizio di OAuth2 configurato (Google).

## Permessi/Visibilità delle registrazioni

I file delle registrazioni sono di default visibili con tutte le persone del dominio specificato nelle configurazioni.

## Cancellazione delle Registrazioni

* Le registrazioni cancellate da interfaccia Moodle **NON** vengono cancellate da Google Drive.
* Se si desidera cancellare in maniera definitiva una registraizone occorre farlo sia da Moodle che dal proprio Google Drive
* I file nel cestino di Google Drive vengono cancellati da Google di default dopo 30 giorni.
