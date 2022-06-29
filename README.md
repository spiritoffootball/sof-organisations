# SOF Organisations

Provides provides Organisation Custom Post Types for The Ball website.

## Description

*SOF Organisations* is a WordPress plugin that provides Organisation Custom Post Types and associated functionality for The Ball website.

### Requirements

This plugin requires the following plugins:

* CiviCRM
* CiviCRM Profile Sync
* CiviCRM Event Organiser
* Event Organiser
* Pledgeball Client
* SOF Pledgeball

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

This plugin provides a bare-bones "Organisations" Custom Post Type and Custom Taxonomy. You will need to add any necessary ACF Custom Fields to a Field Group attached to the "Organisations" Custom Post Type. Each Field should be linked to the corresponding CiviCRM Custom Field attached to the Contact Type that is synced to the "Organisations" Custom Post Type.
