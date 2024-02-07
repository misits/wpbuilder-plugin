<?php

namespace Toolkit\models\custom;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use Toolkit\models\Taxonomy;
use Toolkit\models\custom\Event;

class EventCategory extends Taxonomy
{
    const TYPE = 'event_category';
    const SEASON = 'event_season_category';
    const START_SEASON = ''; // Format: YYYY

    public static function register()
    {
        register_taxonomy(self::TYPE, Event::TYPE, [
            'hierarchical' => true,
            'show_admin_column' => true,
            'publicly_queryable' => false,
            'show_in_rest' => true,
            'labels' => [
                'name'              => __('Catégories', ''),
                'singular_name'     => __('Catégorie', ''),
                'search_items'      => __('Rechercher une catégorie', ''),
                'all_items'         => __('Tout les catégories', ''),
                'parent_item'       => __('Catégorie parente', ''),
                'parent_item_colon' => __('Catégorie parente:', ''),
                'edit_item'         => __('Éditer la catégorie', ''),
                'update_item'       => __('Modifier la catégorie', ''),
                'add_new_item'      => __('Ajouter une nouvelle catégorie', ''),
                'new_item_name'     => __('Nouvelle catégorie', ''),
                'menu_name'         => __('Catégories', ''),
            ]
        ]);

        register_taxonomy(self::SEASON, Event::TYPE, [
            'hierarchical' => true,
            'show_admin_column' => true,
            'publicly_queryable' => false,
            'show_in_rest' => true,
            'labels' => [
                'name'              => __('Saison', ''),
                'singular_name'     => __('Saison', ''),
                'search_items'      => __('Rechercher une saison', ''),
                'all_items'         => __('Toutes les saisons', ''),
                'parent_item'       => __('Saison parente', ''),
                'parent_item_colon' => __('Saison parente:', ''),
                'edit_item'         => __('Éditer la saison', ''),
                'update_item'       => __('Modifier la saison', ''),
                'add_new_item'      => __('Ajouter une nouvelle saison', ''),
                'new_item_name'     => __('Nouvelle saison', ''),
                'menu_name'         => __('Saisons', ''),
            ]
        ]);

        // Generate default season
        if (self::START_SEASON !== '') {
            self::newSeason(self::START_SEASON);
        } else {
            self::newSeason();
        }
    }

    public static function getSeasons()
    {
        return get_terms([
            'taxonomy' => self::SEASON,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);
    }

    public static function newSeason($start = null)
    {
        // Auto generate season by Year-Year
        $currentYear = date('Y');
        $startYear = $start ?? $currentYear;

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $nextYear = $year + 1;
            $seasonName = $year . '-' . $nextYear;

            // Check if the season already exists
            $check = get_term_by('name', $seasonName, self::SEASON);
            if (!$check) {
                // Season does not exist, so create it
                wp_insert_term($seasonName, self::SEASON);
            }
        }
    }
}
