=== Pushover for WooCommerce ===
Contributors: growdev, shopplugins
Donate link: https://shopplugins.com
Tags: woocommerce, pushover, ecommerce, notification, integration, ios, android, store manager
Requires at least: 3.5
Tested up to: 4.7.5
Stable tag: 1.0.12
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Pushover for WooCommerce integrates WooCommerce with the Pushover notifications app for Android and iOS.  

== Description ==

Pushover for WooCommerce integrates WooCommerce with the Pushover notifications app for Android and iOS.  
After installation and setup automatic notifications can be sent to your device for new orders, low stock, backorder and out of stock notifications.

Follow this plugin on [GitHub](https://github.com/shopplugins/pushover-for-woocommerce).

***Translations and pull requests are welcome!***

== Installation ==

Here is a video of installation

[youtube http://www.youtube.com/watch?v=eVOi8cZxU8I]

Step 1. Install the extension

 * Login to your WordPress dashboard. Click on Plugins | Add New from the left hand menu
 * Click on the "Upload" option, then click "Browse" to select the zip file from your computer.
 * After the zip file has been selected press the "Install Now" button.
 * On the Plugins page, find the row for the "Pushover for WooCommerce" plugin and press "Activate"

Step 2. Setup Pushover Account

Prerequisites:

 * To receive notifications you will need to purchase the Pushover App for Android (http://pushover.net/clients/android) or iOS (http://pushover.net/clients/ios)

 * You will also need to create an account at http://pushover.net


1. Register for an account on pushover.net
 1a.  Login to your account at pushover.net
 1b.  On your dashboard the first field in the "Your User Key" section is your User API Token

2. Setup Website App
 2a.  To setup your WooCommerce site go to https://pushover.net/apps
 2b.  Press "Create a New Application"
 	- Add a name
 	- Set Type to "Website"
 	- Add a description
 	- Set URL to the base URL of your WooCommerce site
 	- Optional: Choose an image URL to use for an Icon.
 	- Press "Create Application"
 2c.  The API Token/Key for your application is your Site API Token  

3. Optional:  Login to your device and add a name for it.

Step 3. Setup WooCommerce Pushover Integration

 1.  Login to your WordPress site
 2.  Go to WooCommerce > Settings and click on the Integration tab
 3.  Click on the "Pushover" link
 4.  Click the "Enable" checkbox to turn on notifications
 5.  Set the Site API Token to the value from 2c. above
 6.  Set the User API Token to the value from 1b. above
 7.  Optionally add a device name to limit notifications to.  If left blank notifications will go to all devices.
 8.  Under "Notifications" check the box next to any notifications you want to receive on your device
 9.  Optionally press "Send Test Notification" to test your settings
 10. Press "Save changes"

== Frequently Asked Questions ==



Q.  Is there a limit to the number of notifications I receive? 
A.  The current limit of notifications is 7,500 per month

Q.  What is the length of a notification?
A.  Notifications are limited to 512 characters, including a title.  URL's are limited to 500 characters, URL titles are limited to 50 characters.

== Screenshots ==

1. screenshot-1.jpg
2. screenshot-2.png

== Changelog ==

2015.05.28  1.0.12
* Update for getting product title.

2015.05.16  1.0.11
* Improvement to settings page
* Fix Documentation and plugin links
* Added priority to set message priority ref: https://pushover.net/api#priority

2015.11.16  1.0.10
* Fixed multiple new order sends when PayPal Standard is used

2015.07.31  1.0.9
* Verified WooCommerce 2.4.0 compatibility
* Updated links

2015.02.28   1.0.8
* Added better logging functions.
* Updated branding

2014.12.10   1.0.7
* WordPress 4.0.1 testing
* Added plugin icon

2014.09.17   1.0.6
* WooCommerce 2.2 compatibility

2014.04.04   1.0.5
 * Fixed admin settings page link
 * Fixed send test message redirect page
 * Fixed text domain

2013.11.21   1.0.4
 * Added notification when an order totals $0.

2013.10.19   1.0.3
 * Updated branding and links

2013.08.29   1.0.2
 * Fixed low and no stock notifications hook.
 * Fixed currency symbol - now sending symbol for WooCommerce base currency

2013.02.06   1.0.1
 * Fixed fatal error from undefined is_woocommerce_active() function call

2013.01.18   1.0.0
 * First Version

== Upgrade notice ==




