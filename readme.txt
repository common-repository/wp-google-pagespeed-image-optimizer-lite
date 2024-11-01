=== WP Google PageSpeed Image Optimizer Lite ===
Contributors: r3dridl3
Tags: 	compress, google, pagespeed, image, compression, optimization
Requires at least: 4.3
Tested up to: 4.8.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
This plugin is for all those people who are struggling with Google saying their images can be optimized better. We make use of the Google PageSpeed API. Therefor it has the exact same compression as Google PageSpeed. It just works out of the box (if API key is set).
 
== Description ==

This plugin will optimize your images exactly like Google Pagespeed Insights

This plugin is for all those people who are struggling with Google saying their images can be optimized better.

We make use of the Google PageSpeed API. Therefor it has the exact same compression as Google PageSpeed. It just works out of the box (if API key is set).

The best of it, we do NOT make the image a lower quality one!
**The plugin will NOT resize images! If your theme serves image too big and resizes them by simply scaling it with CSS than there is a change it will not optimize as you might think!**

If you want a demonstration for a specific image, just ask me and I will upload it in my site and show you the results.

For the premium version check <a href="https://codecanyon.net/item/wp-google-pagespeed-insights-image-optimizer/20596917">WP Google PageSpeed Insights Image Optimizer Pro</a>

Here you get:

* Bulk optimize
* Backup your originals
* Restore originals
* Optimize theme images
* Before / after slider (see the comparison)
 
== Installation ==

Upload the plugin and activate it.

After activation you will need to get an PageSpeed Insights API key from console.developers.google.com.

If you have no project yet here, first create a project. After you have done this you can see the Library menu on the left side. Once you click it search for: PageSpeed Insights API. It's under the "Other popular API's". Click this link. There you can Enable the API for this project.

Once enabled you will need to set Credentials for the project. This is allso in the left side menu. You will see a button here "Create credentials" click it and choose "API key". After this your key will be generated. **Restricted API keys are now mandatory**

Copy your API key and go back to your WP admin and go to "Media" -> "Google Image Optimizer Settings". Here you can save the API key.

The API does have it's limits:
There is a maximum of 25000 calls per day to this API and a max of 100 requests every 100 seconds.

== Screenshots ==

1. Savings view
2. Bulk view (premium)
3. Settings view

== Changelog ==

**1.3.4**

* Added new Premium version functions to show what the Premium is capable of.

**1.3.2**

* Added extra check to see if the downloaded ZIP even has an image. If not, nothing happens.

**1.3.1**

* Fixed an error after activation

**1.3**

* Changed how files are downloaded from Google and unzipped.
* Restricted API keys are now mandatory
* Added ALL image sizes crop!

**1.0.5**

* Fixed a few bugs