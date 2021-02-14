=== Embed Sendy ===
Contributors: mauryaratan, codestag, analogwp
Donate link: https://codest.ag/st-donate
Requires at least: 4.9
Tested up to: 5.6.1
Stable tag: 1.3.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.6
Tags: sendy, embed, subscription form, gutenberg, widget, shortcode, codestag, mauryaratan

Embed Sendy subscription form, through a widget, shortcode, or as a Gutenberg block.

== Description ==
Embed Sendy allows you to embed subscription forms for [Sendy](https://codest.ag/sendy) through different methods, including a widget, shortcode, or as a Gutenberg block.

= Features =
* **NEW**: Added Google Recaptcha support
* **NEW**: Customize Form Field Labels
* Support multiple lists, so you can offer subscription for different lists in different scenarios
* AJAX submission for forms, option to enable/disable
* Comes with default form styles, with option to disable
* Automatically add the subscription form before/after each post or page.
* Add content to before/after form

= Why another Sendy plugin? =
I am aware that there are a number of plugins already available for Sendy. *Embed Sendy* focuses on flexibility add the form in different scenarios where it can be useful and convert more subscribers.

== Frequently Asked Questions ==

= Installation instructions =
* Activate the plugin
* Go to Settings > Embed Sendy and configure the options
* Insert the form through a widget, shortcode or Gutenberg block.

= How do I use the widget? =
Head to Appearance > Widgets, and look for 'Embed Sendy' widget and insert it in the desired widget area. You may also choose which list mailing list to subscriber users to.

= How do I use the shortcode? =
You can use the shortcode with `[embed_sendy]` tag. It also accepts a `list` argument with list ID as its value.

= How do I use the Gutenberg block? =
Head to the Gutenberg editor and look for 'Embed Sendy' block. You can also search for `sendy`, `form`, or `newsletter` while adding the block.
Current the Embed Sendy block offers following options:

* Mailing list
* Background Color
* Text Color

= How do I change text before/after the form =
There is a specific section to edit the default texts that appears before and after the form. To change it, head to Settings > Embed Sendy > Form Settings.

= Does this plugin include Sendy? =
No. Sendy is sold separately, please head to [Sendy](https://codest.ag/sendy) website to purchase a license and installation instructions. This plugin simply offer integration to Sendy.

== Screenshots ==
1. Embed Sendy plugin's basic settings screen.
2. Embed Sendy plugin's form settings screen.
3. Embed Sendy widget.
4. Embed Sendy Gutenberg block.

== Changelog ==

= 1.3.3 =
* New: Add support for Google Recaptcha v3

= 1.3.2 =
* Fix: Conflict with commit submit button ID on single posts/pages
* Fix: Incorrect type when returning subscribers (props @AdelDima)
* Improvements: PHP7.4 compatibility

= 1.3.1 =
* Fix: GDPR field always returning false
* Fix: Fix undefined notice when GDPR field is off

= 1.3.0 =
* New: Options to customize form field labels
* New: Added uninstall.php to remove plugin options on uninstall
* Fix: Error on plugin activation for first time (props @ebinnion)
* Fix: Ensure default option for setting is always returned
* Fix: Make name field optional during submission
* Improve: Change translation domain to match plugin slug (props @Tomáš Jenej)
* Improve: Display full name in name field instead of username for logged-in users

= 1.2.1 =
* Fix: Error on activation if options are empty, show a notice instead.

= 1.2 =
* New: Added support for Google Recaptcha 🎉
* Improve: Much improved form validation and security
* Improve: Use JavaScript submission by default and as only way
* Improve: Show form messages before submit button
* Fix: Form not showing up the errors on frontend
* Fix: Honeypot field

= 1.1 =
* New: Added support for Name field
* New: Added support for GDPR consent
* Fix: Issue with honeypot field
* Fix: Set correct data for 'referrer' field
* Fix: Possible thrown errors when no sendy lists exist
* Improve: Compatible upto WordPress v5.2
* Improve: Sendy Gutenberg block to show/hide new Name/GDPR fields.
* Improve: Better error handling when no lists are set
* Improve: Better and secure ajax submission

= 1.0.1 =
* Tweak: Compatibility with Gutenberg 3.5
* Fix: Incorrect Honeypot field

= 1.0.0 =
* Initial release

== Upgrade notice ==

= 1.1 =
- Added new Name and GDPR fields and several bug fixes.

= 1.2 =
- Google Recaptcha support. Much improved form security & validations.

= 1.2.1 =
- Fixes an error activation
