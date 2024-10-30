=== Mobilizon Mirror ===
Contributors: andremenrath
Tags: mobilizon, events, calendar
Donate link: https://liberapay.com/graz.social/
Requires at least: 5.8
Tested up to: 6.1.1
Stable tag: 1.1.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display the events you manage via Mobilizon on your WordPress-site.


== Description ==

This plugin syncs [Mobilizon](https://joinmobilizon.org/) events via creating it's own read only custom post type inside WordPress. You can choose any Mobilizon-group on any Mobilizon-instance that you like.


== Features ==

*   It is designed to integrate well into your theme, though you may override the archive and single pages for the mobilizon_event post type in your of from your theme.
*   Archive Page Styles: From WordPress 6.0 on the prefered way is to use the Query-Loop-Block, but it is also possible to use the custom template for the custom post type archive.
*   Single Event Page Styles: Fits well in your theme, or you may choose beetwen to custom templates  with different positions of featured image
*   Recognizes if an event has been updated or deleted on Mobilizon
*   Efficient: Only fetches the data it need and caches most things.


== Installation and Setup ==

1. Activate the plugin through the 'Plugins' menu in WordPress
2. Go to the settings page (you can also find it in the admin-menu below your Posts and Pages)
3. Enter the Mobilizon-instance domain and your group identifier. You can also simply paste your group-URL like https://mobilizon.any/@examplegroup an the plugin will do the rest. Click the Save button.


== Screenshots ==

1. Archive page with card style
2. Archive page with simple list style
3. Event with image as header
4. Event with image in sidebar
5. Settings Page


== Frequently Asked Questions ==

=  Where do I find the mirrored events on my website? =
You can add an custom event list on any page you wish. It is prefrerred to usage a custom Query-Loop-Block and filter it on Mobilizon Events. Additional you can find the archive link on the settings page.

=  Does the plugin conflict with other Event-Plugins or Post-Types? =
By default this plugin uses a prefixed post type called mobilizon_event, but the slug events, may conflict and cause problems. This may be addressed in the future.

= How often are the events synced? =
You can choose an interval between 2 and 60 minutes.

= How can I add my synced events to the navigation menu of my website? =
In your admin navigation menu go to "Appearance"->"Menu". Then make sure that the "Screen Options" (accessable on the top right) "Mobilizon Events" are marked as visible. Then under "Add menu items" you can select "Mobilizon Events"->"View All"->"Mobilizon Event List". Then you can choose the Navigation Label (the name as it appears for your sites visitors) by yourself.


== Development ==
The source code for development is hosted at [codeberg.org](https://codeberg.org/linos/mobilizon-mirror).
Everyone is invited to contribute.


== Changelog ==

= 1.1.3 =
* Fix old single group setting breaks new installations

= 1.1.2 =
* Fix bug in the custom sync interval
* Tested up to WordPress 6.1

= 1.1.1 =
* Fix sync interval boundaries and display
* Fix event sync failing via very basic task management not to fetch all new events at once
* Fix sync more than 10 future events (up to 1000 for now)

= 1.1.0 =
* Add feature to sync multiple groups
* Add setting to use current theme templates via just modifying the post content
* Add adjustable sync interval
* Fix instance list in admin menu
* Tested up to WordPress 6.0

= 1.0.1 =
* Fix that events with no end-time set have not been mirrored.

= 1.0.0 =
* Initial Release




