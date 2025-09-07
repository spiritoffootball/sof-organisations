# SOF Organisations

Provides provides Organisation Custom Post Types for the Spirit of Football websites.

## Description

*SOF Organisations* is a WordPress plugin that provides Organisation Custom Post Types and associated functionality for Spirit of Football websites.

### Requirements

When enabling Pledgeball integration, this plugin requires the following plugins:

* [CiviCRM](https://docs.civicrm.org/installation/en/latest/wordpress/)
* [CiviCRM Profile Sync](https://wordpress.org/plugins/civicrm-wp-profile-sync/)
* [CiviCRM Event Organiser](https://github.com/christianwach/civicrm-event-organiser)
* [Event Organiser](https://wordpress.org/plugins/event-organiser/)
* [Pledgeball Client](https://github.com/spiritoffootball/pledgeball-client)
* [SOF Pledgeball](https://github.com/spiritoffootball/sof-pledgeball)

## Installation

There are two ways to install from GitHub:

### ZIP Download

If you have downloaded this plugin as a ZIP file from the GitHub repository, do the following to install the plugin:

1. Unzip the .zip file and, if needed, rename the enclosing folder so that the plugin's files are located directly inside `/wp-content/plugins/sof-organisations`
2. Activate the plugin.
3. Configure the settings.
4. You're done.

### `git clone`

If you have cloned the code from GitHub, it is assumed that you know what you're doing.

## Setup

This plugin provides three bare-bones Custom Post Types and Custom Taxonomies:

* Organisations
* Partners
* Ball Hosts

These can be individually enabled on the "Settings &rarr; SOF Organisations" page.

You will need to add any necessary ACF Custom Fields to a Field Group attached to the Custom Post Types. Each Field should be linked to the corresponding CiviCRM Custom Field attached to the Contact Type that is synced to the Custom Post Types.

### Pledgeball integration

This only applies to "The Ball 2022" at the moment. All the Custom Post Types above will need to be enabled.
