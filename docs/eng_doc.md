## ⚠️ Before installing - Mandatory initial configurations

**Before installing** the plugin you will need to make some initial configurations.

### First configure the project on Google Cloud: ### 

1. **Create a project** within the [Google Cloud Platform](https://console.cloud.google.com/),

2. From the API and Services Menu **enable the necessary APIs**: 

   _Google Meet_

   _Google Drive_.

4. **Create new credentials** *ID client OAuth 2.0* of type "Web Application".
   
5. While configuring these credentials, **add the following URI to the section** *authorized redirection URI*: 
   **{your_moodle_url}/mod/gmeet/oauth2callback.php**.

### On Moodle ### 

1. _Site Administration_ > _OAuth 2 Services_ > Enable OAuth 2.

2. In the configurations enter: **Client ID** and **Secret** retrieved in previous steps.

3. In the Plugin configuration: Specify the configured OAuth2 service (Google).

## Permissions/Visibility of Registrations.

Registration files are by default visible with all people in the domain specified in the configurations.


## Delete recordings

* Registrations deleted from Moodle interface **NOT** are deleted from Google Drive.
* If you want to permanently delete a record you must do so from both Moodle and your Google Drive.
* Files in the Google Drive recycle bin are deleted by Google by default after 30 days.


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
