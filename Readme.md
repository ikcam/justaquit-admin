JustAquit Linode Admin Center
=============================

This plugins allows you to create accounts into you Linode Server saving you the trouble to make this steps:

* Create Linode DNS Record.
* Create folder on the server.
* Create a database with a unique userfor each database.
* Create Virtual Host on Apache.
* Activate the VirtualHost.
* Reload and Restart Apache.
* GitHub and BitBucket integration

As you can see this plugin is the perfect tool to manage your Linode Server, saving you a lot of time.


Requirements
------------
Before go to settings run this commands on the bash of your Linode Server:
		
* `$ sudo pear install Net_URL2-0.3.1`
* `$ sudo pear install HTTP_Request2-0.5.2`
* `$ sudo pear channel-discover pear.keremdurmus.com`
* `$ sudo pear install krmdrms/Services_Linode`

Your WordPress installation should be running as `root` and your `wp-config.php` "user" and "password" should be ones of your MySQL `root` otherwise won't see the magic of this script.

Firsts tasks:
-------------

* Go to settings and setup your Linode API Key.
* Your Linode IP number.
* Folder locations.
* User owner.
* Database prefix.

Comming soon:
-------------
* Use `wp_nonce_field` in forms.
* Add test mode.
* Use [Name.com](http://name.com) API for domain management.
* More Linode functions.
* Add/manage clusters.

License: GPL2