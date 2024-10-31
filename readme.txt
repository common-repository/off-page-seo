=== Off Page SEO ===
Contributors: proka
Stable tag: trunk
Plugin URI: https://offpageseo.io/
Description: Turn your WordPress site into the ultimate SEO tool! Monitor SERP progress, track your backlinks.
Version: 3.0.1
Tags: seo, off page, backlinks, serp, google, rank reporter, rank checker, rank, guest posting, guest blogging, reciprocal check, track backlinks, backlinks costs, tracker, rankings
Tested up to: 5.7.1
Requires at least: 5.5.0
Author: Jakub Glos
Author URI: https://offpageseo.io/
License: GPLv3

Turn your WordPress site into the ultimate SEO tool! Monitor ranking progress and track your backlinks.

== Description ==

Let's build better websites with Off Page SEO plugin!

You can read more about the plugin on our website: [Off Page SEO](https://offpageseo.io/).

= Features =
* **Rank Reporter** - periodically check Google rank positions, get detailed analysis in relation to your link building activities
* **Backlink management** - record backlink, track if link is still present, get overview of total and monthly expenses
* **Google Search Console** - display data directly in your WordPress!

A) Free Rank Check Mode

In free mode, your server issues requests on Google and tries to get the positions periodically every 10 minutes. However, Google can prevent your server from seeing the search results. This is why we introduced Premium feature.

B) Premium Rank Check Mode

In premium mode, every time you want to check the positions, the plugin will use REST API to send us a list of keywords along with some other settings (Google domain and language). We do the work on our end. Once all keywords are processed, we use REST API again to send you back the results.

You can set up a frequency in which you want to check the keywords to 1-7 days.



== Installation ==

Installation

1. Download and install plugin.
2. Set up your country in Settings tab.
3. Add keywords and start tracking your positions

== Frequently Asked Questions ==

== Screenshots ==
1. Keyword track dashboard
2. Single keyword graph & analysis
3. Backlinks dashboard
4. Add new backlink
5. Google Search Console

== Changelog ==

= 3.0.3. =
* SSL verify false

= 3.0.1. =
* Statistics to backlinks, pagination for keywords

= 3.0.0. =
* Plugin rewritten from scratch.

= 2.2.22. =
* Fixing assets bug on premium version.

= 2.2.21. =
* Solving downgrading of premium version on update. For more details see http://www.offpageseoplugin.com/activate-premium/

= 2.2.2. =
* Removing Twitter share API query (no longer supported by Twitter)
* Simplifying overall share query
* Adding Spanish translation

= 2.2.1. =
* Replacing Zalmos with Hide My Ass - using this service, plugin checks 3 different IPs
* Additional data from Google API to the plugin's dashboard sidebar
* Adding warning if max_execution_time is about to be reached on rank control
* Add icon to Admin Bar for quick access

= 2.2.0. =
* Going premium
* Adding opportunities
* Fixing minor, mostly UX, bugs

= 2.1.2. =
* Couple minor bug fixes (multisite activation)
* Preloader for adding a keywords :)
* New organic traffic metrics in Dashboard sidebar
* Sorting backlinks in keywords from latest

= 2.1.1. =
* PHP version check

= 2.1.0. =
* Free release
* Plugin rewritten from scratch to be more solid
* Removing some functionality - analyze competitors, alexa and google rank, knowledge base, new backlink ideas, we might add any of it later
* Two new methods of getting rankings added - google ajax api and zalmos. Plugin will know which one to use automatically.
* Enhanced email reporting (emails you when the backlink is deleted on reciprocal control, better rank report email design)
* WP CRON used instead of ajax for all actions
* Google Webmaster Tools integration
* Backlinks are now checked in batch of 5 per trigger
* Adding translations to the plugin
* Adding new keywords moved to the posts / pages or other custom post types you select

= 2.0.3. =
* Plugin licence change

= 2.0.2. =
* Alexa Rank class condition fix
* Social Shares message fix

= 2.0.1. =
* Social share counts reduced to stop hitting hosting limits - you need to activate it first
* Minor bugs fix

= 2.0.0. =
* Going premium, in free mode 2 keywords are available
* New branding images
* Reciprocal backlink check
* Total backlink costs
* Counts how many people came to your site using reported backlinks
* Comments for backlinks
* Enhanced graphs, anlytic tools
* Datepicker for the reported backlink
* Social shares with 0 shares are hidden by default

= 1.2.0. =
* Social Share counter for individual pages,
* minor update of rank reporter.

= 1.1.5. =
* Ajax call for the position update is used since now,
* api add site return url fixed,
* option to leave Guest Posting network.

= 1.1.4. =
* Functionality added - you can note where you gained links

= 1.1.3. =
* Minor social share bug fixed

= 1.1.2. =
* Bug fixes, file_get_contents replaced with curl

= 1.1.1. =
* Analyze competitor tool bug fix

= 1.1.0. =
* Lot of new features added - guest posting, dashboard widgets, other link opportunitites

= 1.0.0. =
* Initial release of the plugin.
