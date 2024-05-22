# WPbuilder Plugin

## Project Leaders

Martin IS IT Services
<hello@misits.ch>

## Description

This plugin allows for loading the basic features of the theme and add a custom wp-admin theme to the WordPress admin panel. The plugin is designed to work with the [WPbuilder Boilerplate](https://github.com/misits/wpbuilder-boilerplate) wich is private and not available to the public, but you can implement your own theme support based on the plugin.

- Activation of maintenance mode
- Creation of custom post types (CPT) and enabling/disabling them
- Creation of custom blocks (Gutenberg)
- Management of WordPress menus

## Technical Documentation

### WordPress Requirements

- Requires at least: 6.0
- Requires PHP: 8.0
- Theme: [WPbuilder Boilerplate](https://github.com/misits/wpbuilder-boilerplate) or implement your own theme support

### Installation

Download the [wpbuilder-plugin](https://github.com/misits/wpbuilder-plugin) as a zip file and install it via the WordPress admin panel.

## Updates

The plugin integrates an update system based on [plugin-update-checker](https://github.com/YahnisElsts/plugin-update-checker) and is linked to the plugin's git repository at [wpbuilder-plugin](https://github.com/misits/wpbuilder-plugin).

To update the plugin, change the version in `readme.txt` and `wpbuilder-plugin.php` and push the changes to the git repository. The plugin will automatically detect updates on sites using the plugin and will offer the update.

## TODO

- [ ] More default CPTs
- [ ] More default blocks
- [ ] Default layouts to fasten the development process

## Looking for a web hosting provider?

<a href="https://www.infomaniak.com/goto/fr/hosting.web?utm_term=664daa56ccbad"><img src="https://affiliation.storage5.infomaniak.com/banners/leaderboardhebergement_fr.jpg"></a>
