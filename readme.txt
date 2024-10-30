=== ClayHR Job Board ===
Tags: api, careers, bizmerlinhr, clayhr, job board, resume
Requires at least: 5.2
Tested up to: 6.2.4
Requires PHP: 7.2
Stable tag: 2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

This Plugin enables you to pull jobs from ClayHR JobBoard and display them on your WordPress site. In this process, you use a shortcode [BizMerlin_job_listings] with your subdomain to pull the data.

= How it works =

1. Please add a registered ClayHR Subdomain under Settings->ClayHR Settings.
2. It will generate the corresponding ClayHR JobBoard URL which will look something like this:
    YOUR_ENTERED_SUBDOMAIN.bizmerlin.net/jobboard Then, the plugin will pull all open job positions from ClayHR Jobboard [To take a look at the Terms of Service, please visit this link: https://www.bizmerlin.com/terms-of-service/ ]

== Installation ==

= Traditional Installation =
1. Go to: Plugin > Add New menu.
2. Here, search for 'ClayHR Job Board'
3. Now click on 'Install' and then 'Activate'
4. Add your Subdomain under BizMerlinHR Settings which you will find under Settings Menu.
5. Add ShortCode [BizMerlin_job_listings] to the part on your website where you want all your open positions to be shown.

= Manual Installation =
<ol>
	<li>Download plugin zip file from : https://wordpress.org/plugins/bizmerlinhr-jobboard/</li>
	<li>Open Dashboard of your WordPress website.</li>
	<li>Open: Plugins > Add New > Upload Plugin > Browse... (select downloaded zip file) > Install Now</li>
	<li>Now click on 'Activate'.</li>
	<li>Add your Subdomain under ClayHR Settings which you will find under Settings Menu.</li>
	<li>Add ShortCode [BizMerlin_job_listings] to the part on your website where you want all your open positions to be shown.</li>
</ol>

== Screenshots ==

1. Add your Subdomain under ClayHR Settings which you will find under Settings.
2. Add ShortCode [BizMerlin_job_listings] to the part on your website where you want all your open positions to be shown.
3. Open positions shown in website.

== Changelog ==

= 2.0 =
* Tested plugin for new wordpress release

= 1.9 =
* Changed to ClayHR Branding

= 1.8 =
* Added Name And Location Search

= 1.7 =
* Fixed Position Apply URL's 

= 1.6 =
* Added Location And Application Due Date Information

= 1.5 =
* Support for Wordpress 6.1

= 1.4 =
* Support for Wordpress 6

= 1.3 =
* Fixed position embed issue

= 1.2 =
* Fixed some issues

= 1.1 =
* Minor Fixes

= 1.0 =
* Initial Release

<?php
/**
 * @package BizMerlinHR JobBoard
 */
/*
Plugin Name: ClayHR Job Board
Description: Plugin to pull jobs from ClayHR JobBoard and display it on your WordPress site.
Version: 1.9
Author: ClayHR
Author URI: https://clayhr.com/
License: GPLv2 or later
*/
?>