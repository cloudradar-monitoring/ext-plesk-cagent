# CloudRadar extension for Plesk

## Install

### Option1
Install `Panel.ini` extention and open it.
Find line containing 
```
[ext-catalog]
extensionUpload = false
```
and replace it with 
```
[ext-catalog]
extensionUpload = true
```
Most probably you will not have the mentioned setting. So insert it at the end of the file and click on "Save".

### Option2
Login to your server via SSH.
Edit `/usr/local/psa/admin/conf/panel.ini` and add the following block at the end of the file

    [ext-catalog]
    extensionUpload = true

After that use the Plesk UI. Go to Extensions->My Extensions. Now use the button "Upload Extension" and upload the ZIP file you have downloaded from https://github.com/cloudradar-monitoring/ext-plesk-cagent/releases
