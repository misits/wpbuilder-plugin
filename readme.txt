=== WPbuilder Plugin ===
Contributors: Martin IS IT Services
Tags: wpbuilder, theme, plugin, custom post type, block, gutenberg, menu, misits
Requires at least: 5.0
Tested up to: 6.4.3
Requires PHP: 8.0
License: GPL v2 or later

WPbuilder est un plugin qui fournit des fonctions utiles pour le développement de thèmes Wordpress.

== Description ==
Ce plugin permet de charger les fonctionnalités de base du thème.

- Activation du mode maintenance
- Creation de custom post type (CPT) et activation/désactivation de ceux-ci
- Creation de block gutenberg
- Gestion des menus wordpress

== Installation ==
Télécharger le plugin [wpbuilder-plugin](https://github.com/misits/wpbuilder-plugin) en tant que zip et l'installer via l'administration de Wordpress.

Le theme compatible avec le plugin est [wpbuilder-boilerplate](https://github.com/misits/wpbuilder-boilerplate).

== Changelog ==

= 1.5.3 =
- Update Product model methods

= 1.5.2 =
- Update carbon fields for maintenance mode

= 1.5.1 =
- Update script & css to avoid blocking render

= 1.5.0 =
- Add webp support by default

= 1.4.2 =
- Fix crb empty field check

= 1.4.1 =
- Fix custom post type Project labels in complex carbon fields

= 1.4.0 =
- Add custom post type for Project
- Add Folder taxonomies for Media Library

= 1.3.1 =
- Update custom post type Haircut

= 1.3.0 =
- Add custom post type for Haircut

= 1.2.2 =
- Avoid add to carte redirection

= 1.2.1 =
- Add admin panel style

= 1.2.0 =
- Custom admin theme

= 1.1.1 =
- Matomo widget fix wp option nonce

= 1.1.0 =
- Matomo widget & sys info

= 1.0.6 =
- Fix fields in Gallery model

= 1.0.6 =
- Add new method to Media

= 1.0.5 =
- Allow carbon fields to be used in page and post
- New Gallery function to get images from a gallery

= 1.0.4 =
- Login form update style
- Fix missing title tab

= 1.0.3 =
- Better partners repeater field
- Cookie banner style update

= 1.0.2 =
- Fix admin script tab conflict

= 1.0.1 =
- Ajout maintenance mode partials

= 1.0.0 =
- Initial release.

== Upgrade Notice ==
Le plugin integere un système de mise à jour basé sur [plugin-update-checker](https://github.com/YahnisElsts/plugin-update-checker) et est lié au dépôt git du plugin sur https://github.com/misits/wpbuilder-plugin.

Pour mettre à jour le plugin, il faut changer la version `readme.txt` et `wpbuilder-plugin.php` et pousser les changements sur le dépôt git. Le plugin detectera automatiquement les mis à jour sur les sites utilisant le plugin et proposera la mise à jour.