# Cagent extension for Plesk

## Install

Login to your server via SSH.
Edit `/usr/local/psa/admin/conf/panel.ini` and add the following block at the end of the file

    [ext-catalog]
    extensionUpload = true

After that use the Plesk UI. Go to Extensions->My Extensions. Now use the button "Upload Extension" and upload the ZIP file you have downloaded from https://github.com/cloudradar-monitoring/ext-plesk-cagent/releases
