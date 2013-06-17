=== WPTT ICS Feeds ===

Contributors: Curtis McHale
Tags: calendar, ICS
Requires at least: 3.4
Tested up to: 3.5
Stable tag: 1.1

Subscribe to your published and future posts.

== Description ==

Adds an ICS compatible feed of your published and future posts.

WARNING: If you have thousands of posts this is probably pretty expensive. As the Lorax says:

"You have been warned!" (insert crazy eyes and finger pointing)

== Installation ==

1. Extract to your wp-content/plugins/ folder.

2. Activate the plugin.

3. Subscribe to the feed

== Usage ==


=== Subscribing ===

The url for the calendar subscription is `http://yoursite.com/?feeds=wptticsfeeds`. Change `yoursite.com` to the domain of your site. Simply copy/paste that url in to your calendar programe of choice. Make sure you are trying to subscribe to a calendar by URL. The posts will show up

You can set the author by using the `wpttauthor` query arg and the author nicename. So an author with the name curtis would get a url formatted like `http://yoursite.com/?feeds=wptticsfeeds&wpttauthor=curtis`. That would only put posts from that author on the calendar.

== Changelog ==

= 1.1 =

- added author query param

= 1.0 =

- basic feed that puts published and scheduled posts on your calendar
