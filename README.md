# Twitter Scheduler #
**Contributors:** williampatton  
Tags:
**Requires at least:** 3.7  
**Tested up to:** 5.0
**Stable tag:** 0.1.0  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

This plugin allows users to schedule Tweets (and retweets) in advance and have WordPress send them out for you.

## Description ##

Schedule a Tweet for some time in the future and WordPress will send it out for you around the specified time.

*   You can setup normal Tweets or Retweets.
*   Attach an image to the Tweet with a featured image.
*   Allows you to set it to 'farthest in the future' and it will be scheduled some time after the farthest away Tweet.

## Installation ##

1. Upload the plugin and activate it.
1. Add your details in the settings page (needs twitter app auth tokens).
1. Schedule your Tweets and see them go out when you set them.

## Frequently Asked Questions ##

### Why are my tweets not going out at the right time? ###

The plugin relies on the WP_Cron system to operate, see https://codex.wordpress.org/Function_Reference/wp_cron. I highly recommend swapping from WP_Cron to a real cron or external cron trigger.

## Screenshots ##

## Changelog ##

### 0.1.0 ###
* Initial version

# Licences #
Twitter API library - TwitterAPIExchange from James Mallison <me@j7mbo.co.uk> - http://github.com/j7mbo/twitter-api-php - Used under MIT licence.
twitter-text - A tweet parser library by Twitter, Inc - https://github.com/twitter/twitter-text - Used under Apache 2.0 licence.
