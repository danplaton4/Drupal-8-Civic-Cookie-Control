Cookie control module for drupal.

=== Civic Cookie Control 8 ===
Contributors: aperperis, ralliaf
Plugin Name: Civic Cookie Control 8
Widget URI: https://www.civicuk.com/cookie-control 
Author URI: https://www.civicuk.com
Author: Civicuk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Composer ==

`composer require 'civic/civiccookiecontrol'`

This module enables you to comply with the UK and EU law on cookies.

== Description ==

This Drupal plugin simplifies the implementation and customisation process of Cookie Control by [Civic UK](https://www.civicuk.com/).

With an elegant user-interface that doesn't hurt the look and feel of your site, Cookie Control is a mechanism for controlling user consent for the use of cookies on their computer.

There are several license types available, including:

**Community edition** - Provides all of the core functionality of Cookie Control, and is of course GDPR compliant. You can use it to test Cookie Control, or if you don't require any of its pro features.

**Pro edition** - Includes all of the pro features for use on a single website, priority support and updates during your subscription. 

**Multisite Pro Edition** - Offers all of the pro features for use on up to ten websites, priority support and updates during your subscription.

**Pro edition** and **Multisite Pro Edition** support IAB (TCF v1.1).

To find out more about Cookie Control please visit [Civic's Cookie Control home page](https://www.civicuk.com/cookie-control).


**Please Note**:

You will need to obtain an API KEY from [Civic UK](https://www.civicuk.com/cookie-control/v8/download) in order to use the module.

Cookie Control is simply a mechanism to enable you to comply with UK and EU law on cookies. **You need to determine** which elements of your website are using cookies (this can be done via a [Cookie Audit](https://www.civicuk.com/cookie-control/deployment#audit), and ensure they are connected to Cookie Control.


== Installation ==

1. Obtain an API Key from [Civic UK](https://www.civicuk.com/cookie-control/v8/download) for the site that you wish to deploy Cookie Control.*
2. Add the module in the corresponding Drupal folder.
3. Enable the module.
4. Run "drush updb" or update the database from update.php.
5. Configure the module from the 'Configuration->Civic Cookie Control 8' menu.
6. All done. Good job!

* If you already have an API Key and are wanting to update your domain records with CIVIC, please visit [Civic UK](https://www.civicuk.com/cookie-control/v8/download)

== Frequently Asked Questions ==

= API Key Error =

If you are using the free version your API key relates to a specific host domain.

So www.mydomain.org might work, but mydomain.org (without the www) might not.

Be sure that you enter the correct host domain when registering for your API key.

The recommended way of avoiding this problem is to create a 301 redirect so all requests to mydomain.org get forwarded to www.mydomain.org

This may have [SEO benefits](http://www.mattcutts.com/blog/seo-advice-url-canonicalization/) too as it makes it very clear to search engines which is the canonical (one true) domain.

= Is installing and configuring the plugin enough for compliance? =

Only if the only cookies your site uses are the Google Analytics ones. 
If other plugins set cookies, it is possible that you will need to write additional JavaScript.
To determine what cookies your site uses do a a [Cookie Audit](https://www.civicuk.com/cookie-control/deployment#audit). You will need to do this in any case in order to have a compliant privacy policy.
It is your responsibility as a webmaster to know what cookies your site sets, what they do and when they expire. If you don't you may need to consult whoever put your site together.

= I'm getting an error message Cookie Control isn't working? =

Support for Cookie Control is available via the forum: [https://groups.google.com/forum/#!forum/cookiecontrol](https://groups.google.com/forum/#!forum/cookiecontrol/) or open a support ticket in [Support](https://www.civicuk.com/support)

= Update from previous version =

Users with plugin version 8.x-1.0-rc1 (downloaded directly from civicuk.com website) should backup their data, 
delete the older plugin version and download the latest version from civicuk.com website. Then run "drush updb" or visit /update.php. 
Your data will remain intact, however you will have to re assign the third party cookies inside each cookie category and then save your settings. Users with version prior to 1.6 should review all settings and select values for newly created configuration options.


== Changelog ==
= 8.x-2.0-rc1 =
* Added alternative appearance styles for the notify bar's settings button.
* Added encodeCookie property to better support RFC standards and certain types of server processing.
* Added subDomains property to offer more flexibility on how user consent is recorded.
* IAB support (TCF v1.1)

