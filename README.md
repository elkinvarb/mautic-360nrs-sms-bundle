Plugin Name: 360NRS SMS Plugin for Mautic.

Description: A short paragraph explaining what it does.

Compatibility: State that it's compatible with Mautic 6+.

Created with use of IA Gemini and all code SmsBundle of Mautic.

Installation Steps:

Download the latest release as a ZIP file.

Unzip it into the plugins/MauticNrsSmsBundle directory of your Mautic instance.

Clear the Mautic cache (php bin/console cache:clear).

Go to Mautic Settings > Plugins and click "Install/Upgrade Plugins".

Configuration Steps:

Find the "360NRS SMS" plugin in the list.

Enable it and open the configuration.

Under the "Credentials" tab, enter your 360NRS API Token (just the token, without the word "Basic").

Under the "Features" tab, enter the "From Name/Number" you want to use.

Save and close.

For this version, to see the Channel "Text Messages", you need activate Twillio Plugin, isn't necessary insert real credentials.
