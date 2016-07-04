=== What I'm Reading ===
Contributors: NoseGraze
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=L2TL7ZBVUMG9C
Tags: goodreads, books, reading
Requires at least: 3.0
Tested up to: 4.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A widget for displaying books on one of your Goodreads shelves.

== Description ==

A widget for displaying books on one of your Goodreads shelves.

== Installation ==

1. Upload `what-im-reading` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Appearance -> Widgets and add the 'Goodreads Shelf' widget to your sidebar

== Frequently Asked Questions ==

= How do I get a Goodreads API key? =

1. Login to Goodreads and visit their API key page: https://www.goodreads.com/api/keys
1. Fill in the details under "App Info". You can set the Application Name to "Goodreads Shelf" and the Company Name to your blog name. The details don't particularly matter. Leave all the optional fields (application URL, callback URL, and support URL) blank.
1. Click Update App Info to save your settings.
1. Your API key (and secret) will then be shown on the page. Copy the key value and paste it into the widget settings.

= Why isn't my shelf updating? =

The shelves only reload once every six hours.

== Screenshots ==

1. The view of the settings panel.
2. A screenshot of the social share icons automatically added to the Twenty Fifteen theme. This also shows the default button styles applied.

== Changelog ==

= 1.0.1 =
* Minor code adjustments.
* Added a few actions and filters throughout the widget.
* Change instances of $_token to the actual text domain.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==