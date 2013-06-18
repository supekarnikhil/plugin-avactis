plugin-avactis
==============

Paysera.com payment gateway plugin for Avactis

Requirements
------------

- Avactis v2

Installation
------------


1. Download this repository as zip.
2. Extract avactis-system and avactis-themess folder to yout Avactis directory.
3. Go to avactis-system/cache and delete all files.
Refresh payment module list in Admin area, new module should appear.
4. add following line to /avactis-system/shortname2path file.

PM_WTP = "avactis-system/admin/templates/resources/payment-module-wtp-messages-eng.ini"

5. Import data.sql file to your database.
6. Once again go to avactis-system/cache and delete all files.
Refresh payment module list in Admin area, Paysera.com payment module should appear
as active and selected.

When migrating to Paysera.com no testings were made, so if any problems occur please report to support@paysera.com

Contacts
--------

If any problems occur please feel free to seek help via support@paysera.com
