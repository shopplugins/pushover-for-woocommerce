Pushover for WooCommerce
========================

Pushover for WooCommerce integrates WooCommerce with the Pushover notifications app for Android and iOS.  

== Description ==

Pushover for WooCommerce integrates WooCommerce with the Pushover notifications app for Android and iOS.  
After installation and setup automatic notifications can be sent to your device for new orders, low stock, backorder and out of stock notifications.

== Installation ==

Step 1. Install the extension

 * Login to your WordPress dashboard. Click on Plugins | Add New from the left hand menu
 * Click on the "Upload" option, then click "Browse" to select the zip file from your computer.
 * After the zip file has been selected press the "Install Now" button.
 * On the Plugins page, find the row for the "Pushover for WooCommerce" plugin and press "Activate"

Step 2. Setup Pushover Account

Prerequisites:
 * To receive notifications you will need to purchase the Pushover App for Android (http://pushover.net/clients/android)or iOS (http://pushover.net/clients/ios)
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
 2.  Go to WooComerce > Settings and click on the Integration tab
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

2013.02.07	 1.0.1
	* Fixed fatal error from undefined is_woocommerce_active() function call

2013.01.18   1.0.0
	* First Version

