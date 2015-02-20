=== Quotes and Tips ===
Contributors: bestwebsoft
Donate link: https://www.2checkout.com/checkout/purchase?sid=1430388&quantity=1&product_id=94
Tags: additional info, add quotes, background image, create quotes, display quotes, hints, information, quote label, post, quote, qotes, quotes, quotes and tips post, quotes & tips post, quotes from clients, publish quotes, statements, tip, tip label, tips, tips for visitors
Requires at least: 3.0
Tested up to: 4.1.1
Stable tag: 1.23
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to implement the Quotes & Tips block into your web site.

== Description ==

This plugin allows you to create and publish quotes of your customers about your work and helpful tips for the website visitors. Imagine that now on now your satisfied customers will help to promote business. Moreover, it is very simple in using.

http://www.youtube.com/watch?v=kSR0mERfxBI

<a href="http://wordpress.org/plugins/quotes-and-tips/faq/" target="_blank">FAQ</a>

<a href="http://support.bestwebsoft.com" target="_blank">Support</a>

= Features =

* Display: Set a background image for the block, change background color and text color.
* Actions: Add quotes & tips to any place on your website.
* Label: Change a label for the Quotes block and Tips block - user custom label or title of the quotes & tips posts.

= Recommended Plugins =

The author of the Quotes and Tips also recommends the following plugins:

* <a href="http://wordpress.org/plugins/updater/">Updater</a> - This plugin updates WordPress core and the plugins to the recent versions. You can also use the auto mode or manual mode for updating and set email notifications.
There is also a premium version of the plugin <a href="http://bestwebsoft.com/products/updater/?k=e8f05fa90cedfd3a96483e8f0ca60ab5">Updater Pro</a> with more useful features available. It can make backup of all your files and database before updating. Also it can forbid some plugins or WordPress Core update.

= Translation =

* German (de_DE) (thanks to <a href="mailto:arnold@montjoie.de">Arnold Montjoie</a>)
* Hebrew (he_IL) (thanks to Sagive SEO)
* Russian (ru_RU)
* Ukrainian (uk)

If you create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text in PO and MO files</a> for <a href="http://support.bestwebsoft.com" target="_blank">BestWebSoft</a> and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO files <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in another languages won't be accepted.

If you notice any bugs in the plugins, you can notify us about it and we'll investigate and fix the issue then. Your request should contain URL of the website, issues description and WordPress admin panel credentials.
Moreover we can customize the plugin according to your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share with the other users then. 
We can fix some things for free for the users who provide translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation ==

1. Upload the folder `quotes-and-tips` to the directory `/wp-content/plugins/`.
2. Activate the plugin via the 'Plugins' menu in WordPress.
3. The site settings are available in 'BWS Plugins'->'Quotes and Tips'.

<a href="https://docs.google.com/document/d/1LF8JiXTELxGQ-xLbNnc0bUWopxqXkkCgYhGwU6lSsAI/edit" target="_blank">View a PDF version of Step-by-step Instruction on Quotes and Tips Installation</a>.

== Frequently Asked Questions ==

= How to use the plugin? =

1. Click 'Add New' in the 'Quotes' menu and fill your page.
1. Click 'Add New' in the 'Tips' menu and fill your page.
2. Сopy and paste this shortcode to your post or page - `[quotes_and_tips]`, or add the following strings into the template source code
`&lt;?php if( function_exists( 'qtsndtps_get_random_tip_quote' ) ) qtsndtps_get_random_tip_quote();  ?&gt;`. 
3. The settings are available on the page 'BWS Plugins'->'Quotes and Tips'.

= How to change background or text color? =

1. Go to the Settings Page in the menu 'BWS Plugins'->'Quotes and Tips'.
2. Choose `Additional options`.
3. Apply the necessary settings (choose the text color, background color, upload a background image).

= What should I do if I don't want to use a block background image anymore? =

1. Go to the Settings Page in the menu 'BWS Plugins'->'Quotes and Tips'.
2. Choose `Additional options`.
3. Unmark the item `Use background image`.

= I want to choose a background image position. How can I do that? =

1. Go to the Settings Page in the menu 'BWS Plugins'->'Quotes and Tips'.
2. Choose `Additional options`.
3. Apply the necessary settings in the blocks Background image repeat, Background image horizontal alignment, Background image vertical alignment.

= How to use the other language files with Quotes and Tips? = 

Here is an example for the German language files.

1. In order to use another language for WordPress it is necessary to switch the WP version to the required language and in the configuration wp file - `wp-config.php` in the line `define('WPLANG', '');` type `define('WPLANG', 'de_DE');`. If everything is done properly the admin panel will be in German.

2. Make sure that the the files `de_DE.po` and `de_DE.mo` are present in the plugin (in the languages folder which is in the root of the plugin).

3. If there are no such files you should copy the other files from this folder (for example, for the Russian or Italian language) and rename them (you should write `de_DE` instead of `ru_RU` in both files).

4. You can edit the files using the program Poedit - http://www.poedit.net/download.php - please download this program, install it, open the file with this program (the necessary language file) and for each line in English add  translation in German.

5. If everything is done properly all lines will be in German in the admin panel and in the front-end.

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<a href="http://support.bestwebsoft.com" target="_blank">http://support.bestwebsoft.com</a>). If no, please provide the following data along with your problem's description:
1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: <a href="https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit" target="_blank">Instuction on System Status</a>

== Screenshots ==

1. Basic option for Quotes and Tips.
2. Basic and Additional options for Quotes and Tips.
3. Frontend page with Quotes and Tips block.

== Changelog ==

= V1.23 - 19.02.2015 =
* NEW : We added shortcode for displaying Quotes and Tips block.
* Bugfix : Bug with color from text color field misplacement was fixed.

= V1.22 - 26.12.2014 =
* Bugfix : Problem with quotes signature is fixed.
* Update : We updated all functionality for wordpress 4.1.

= V1.21 - 19.11.2014 =
* Update : We have made Quotes and Tips block responsive.

= V1.20 - 08.08.2014 =
* Bugfix : Security Exploit was fixed.

= V1.19 - 13.05.2014 =
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 3.9.1.
* Update : The Ukrainian language is updated in the plugin.

= V1.18 - 14.04.2014 =
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 3.8.2.
* Bugfix : Plugin optimization is done.

= V1.17 - 19.02.2014 =
* Update : Screenshots are updated.
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 3.8.1.
* Bugfix : Problem with empty author field is fixed.

= V1.16 - 26.12.2013 =
* Update : BWS plugins section is updated.
* Update : We updated all functionality for wordpress 3.8.

= V1.15 - 28.11.2013 =
* Update : Tips icons updated.
* Update : BWS plugins section is updated.

= V1.14 - 30.10.2013 =
* NEW : Add checking installed wordpress version.
* Update : We updated all functionality for wordpress 3.7.1.
* Update : Qoutes and Tips icons updated.
* Update : Activation of radio button or checkbox by clicking on its label.
* Bugfix : Default background now is used only if additional settings checked.
* Bugfix : We fixed Qoutes author name and position fields errors.

= V1.13 - 02.10.2013 =
* NEW : The Ukrainian language file is added to the plugin.
* Update : We updated all functionality for wordpress 3.6.1.

= V1.12 - 04.09.2013 =
* Update : We updated all functionality for wordpress 3.6.
* Update : Function for displaying BWS plugins section placed in a separate file and has own language files.

= V1.11 - 18.07.2013 =
* NEW : Added an ability to view and send system information by mail.
* Update : We updated all functionality for wordpress 3.5.2.

= V1.10 - 30.05.2013 =
* Update : BWS plugins section is updated.

= V1.09 - 06.05.2013 =
* NEW : The German language file is added to the plugin.

= V1.08 - 17.04.2013 =
* Update : The English language is updated in the plugin.

= V1.07 - 31.01.2013 =
* Bugfix : The admin menu are fixed.
* Update : We updated all functionality for wordpress 3.5.1.

= V1.06 - 24.07.2012 =
* Bugfix : Cross Site Request Forgery bug is fixed.

= V1.05 - 10.07.2012 =
* NEW : The Hebrew language file is added to the plugin.
* Update : We updated all functionality for wordpress 3.4.1.

= V1.04 - 27.06.2012 =
* Update : We updated all functionality for wordpress 3.4.

= V1.03 - 05.04.2012 =
* Bugfix : The conflict of our javascript with javascript of other plugins is fixed. 
* Changed : BWS plugins section. 

= V1.02 - 12.03.2012 =
* Changed : BWS plugins section. 

= V1.01 - 01.03.2012 =
* NEW : Additional options for pluign are added.
* NEW : The Russian language file is added to the plugin.

== Upgrade Notice ==

= V1.23 =
We added shortcode for displaying Quotes and Tips block. Bug with color from text color field misplacement was fixed.

= V1.22 =
Problem with quotes signature is fixed. We updated all functionality for wordpress 4.1.

= V1.21 =
We have made Quotes and Tips block responsive.

= V1.20 =
Security Exploit was fixed.

= V1.19 =
BWS plugins section is updated. We updated all functionality for wordpress 3.9.1. The Ukrainian language is updated in the plugin.

= V1.18 =
BWS plugins section is updated. We updated all functionality for wordpress 3.8.2. Plugin optimization is done.

= V1.17 =
Screenshots are updated. BWS plugins section is updated. We updated all functionality for wordpress 3.8.1. Problem with empty author field is fixed.

= V1.16 =
BWS plugins section is updated. We updated all functionality for wordpress 3.8.

= V1.15 =
Tips icons updated. BWS plugins section is updated.

= V1.14 =
Add checking installed wordpress version. We updated all functionality for wordpress 3.7.1. Qoutes and Tips icons updated. Activation of radio button or checkbox by clicking on its label. Default background now is used only if additional settings checked. We fixed Qoutes author name and position fields errors.

= V1.13 =
The Ukrainian language file is added to the plugin. We updated all functionality for wordpress 3.6.1.

= V1.12 =
We updated all functionality for wordpress 3.6. Function for displaying BWS plugins section placed in a separate file and has own language files.

= V1.11 =
Added an ability to view and send system information by mail. We updated all functionality for wordpress 3.5.2.

= V1.10 =
BWS plugins section is updated.

= V1.09 =
The German language file is added to the plugin.

= V1.08 =
The English language is updated in the plugin.

= V1.07 =
Bugs in admin menu were fixed. We updated all functionality for wordpress 3.5.1.

= V1.06 =
Cross Site Request Forgery bug was fixed.

= V1.05 =
The Hebrew language file is added to the plugin. We updated all functionality for wordpress 3.4.1.

= V1.04 =
We updated all functionality for wordpress 3.4.

= V1.03 =
The conflict of our javascript with javascript of other plugins is fixed. BWS plugins section. 

= V1.02 =
BWS plugins section has been changed. 

= V1.01 =
Additional options for Settings plugin page are added. Added The Russian language file is added to the plugin.