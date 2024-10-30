=== Newsletter Sign-Up for CleverReach ===
Contributors: cleverreach43
Tags: email, newsletter, cleverreach, campaign, widget, marketing, woocommerce
Requires at least: 3.9
Tested up to: 6.5.2
Stable tag: 2.3.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily integrate a CleverReach Sign-Up form in your website. Supports widget, shortcode, comment integration and template function

== Description ==
Connect your WordPress blog to your [CleverReach Newsletter](https://clvr.ch/D4nkQ) account and insert your Double-Opt-in signup forms on your WordPress with just a few clicks.

= Drag & Drop Form Builder =

* Build your individual Signup-Form via Drag & Drop
* Get custom fields from your CleverReach list
* Phrase your own signup and unsubscribe texts
* Modify all labels and descriptions
* No coding skills required – quickly & easily add your form to your WordPress page via widget or shortcode

= Comment Form Integration =

Display a checkbox in your comment form to allow users to subscribe to your newsletter

= More features of the plugin =

* Double-Opt-In feature – completely GDPR-compliant
* 100% responsive – usable on any smartphone
* Use data fields to find out more about your new email subscribers for your CleverReach recipient list and use these details for sending your newsletters
* Automatic transfer of data to your CleverReach recipient list
* Integration into the comment section
* Detects duplicate entries
* AJAX form submission

= Connection with Contact Form 7 Plugin =

Have a look at our [official CleverReach plugin for WordPress](https://wordpress.org/plugins/cleverreach-wp/) - Contacts that submit the Contact Form 7 form can be synchronized to CleverReach via Double opt in.

= Insert DOI Forms in Your WooCommerce Shop =

Add GDPR-compliant signup forms and much more to the pages of your store (e.g. in the checkout process) with our [official CleverReach plugin for WooCommerce](https://wordpress.org/plugins/cleverreach-wc/).

= Most important features of CleverReach® =

* Easily create and send newsletters with our user-friendly newsletter editor
* Free responsive templates available
* Analyze the success of your mailings with our reporting
* Easily segment your recipients with individual tags
* GDPR-compliant registration forms
* Free email support
* Improve user retention in your sleep with automated newsletter workflows
* Languages: CleverReach® is available in English, German, French, Spanish and Italian.

Email marketing made easy: Use your WordPress data in CleverReach® to create relevant newsletters for your users. Benefit from more opens and more clicks with less effort.


= About CleverReach® =
CleverReach® is one of the leading providers for email marketing and impresses more than 300,000 customers in 170 countries with the effectiveness and simplicity of the software. Founded in 2007, CleverReach® valued the importance of data protection standards, always exceeding legal requirements.

= Disclaimer =
The plugin was developed by Hannes Etzelstorfer. We at CleverReach have adopted the plugin in April 2021. If you have any questions or need help, please feel free to contact our service team at any time.

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

== Frequently Asked Questions ==

= System Requirements =

You need PHP 5.6 or higher as well as CURL to use this plugin.

= New fields are not visible in FormBuilder =

To load new fields from CleverReach or fields added with a plugin update (e.g. privacy policy checkbox) you have to reset your form.
Click the button "Load Form Attributes" to load all available fields.

== Screenshots ==

1. Form Builder
2. Comment Form Integration
3. Works with Gutenberg

== Changelog ==

= 2.3.5 =

- Fix Automatic conversion of false to array

= 2.3.4 =

- Updated the CleverReach icon and logo.

= 2.3.3 =

- Fixed refreshing access token.

= 2.3.2 =

- Adjusted print_cleverreach_form functionality.

= 2.3.1 =

- Add error message for form submission when CleverReach access token is missing.

= 2.3.0 =

- Switching to the latest v3 CleverReach REST API.
- Added CleverReach account ID in the general plugin page.
- Added automatically refreshing an access token.
- Reconnecting the account will remove all previously plugin saved data.
- Added the possibility to update the form template in the form builder section.
- Updated the plugin sidebars.
- Added missing translations for German.
- Improved logging and detecting issues in the plugin.

= 2.2.2 =
Fixed a security issue
thanks to badmaxx

= 2.2 =
Added Gutenberg block for CleverReach form
Added privacy policy checkbox
Fields can be required

= 2.1 =
Improved authetication workflow.
Fixed the empty error message appearing on some installations

= 2.0.4 =
Added additional check for global and local attributes

= 2.0.3 =
Changed API version

= 2.0.1 =
Load CleverReach API PHP class only once (in case there are several CleverReach plugins installed)

= 2.0 =
Changed to CleverReach REST API

= 1.7 =
PHP 7 support

= 1.6 =
Extended API funtion for optional DOI mail

= 1.4 =
Added error message in case of missing email address
[read more](https://wordpress.org/support/topic/wrong-validation-message)

= 1.3 =
Removed Ajax Bug return 0 instead of success/error message

= 1.2 =
Double-Opt-In is mandatory now

= 1.1 =
Added support for PHP 5.2

= 1.0.1 =
Added support for global fields

= 1.0 =
Initial Release
