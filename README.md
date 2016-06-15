# What I'm Reading

A widget for displaying books on one of your Goodreads shelves.

## Installation

1. Upload `what-im-reading` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Appearance -> Widgets and add the 'Goodreads Shelf' widget to your sidebar

## Frequently Asked Questions

**How do I get a Goodreads API key?**

1. Login to Goodreads and visit their API key page: https://www.goodreads.com/api/keys
2. Fill in the details under "App Info". You can set the Application Name to "Goodreads Shelf" and the Company Name to your blog name. The details don't particularly matter. Leave all the optional fields (application URL, callback URL, and support URL) blank.
3. Click Update App Info to save your settings.
4. Your API key (and secret) will then be shown on the page. Copy the key value and paste it into the widget settings.

**Why isn't my shelf updating?**

The shelves only reload once every six hours.

## Changelog

**1.0.0**
* Initial release.