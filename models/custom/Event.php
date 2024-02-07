<?php

namespace Toolkit\models\custom;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use Toolkit\models\CustomPostType;
use Toolkit\models\custom\EventCategory;
use Toolkit\models\QueryBuilder;

class Event extends CustomPostType implements \JsonSerializable
{
    const TYPE = 'event';
    const SLUG = "events";

    public static function type_settings()
    {
        return [
            "menu_position" => 2.2,
            "label" => __("Events", ""),
            "labels" => [
                "name" => __("Events", ""),
                "singular_name" => __("Event", ""),
                "menu_name" => __("Events", ""),
                "all_items" => __("Tous les events", ""),
                "add_new" => __("Ajouter", ""),
                "add_new_item" => __("Ajouter un event", ""),
                "edit_item" => __("Modifier un event", ""),
                "new_item" => __("Nouveau event", ""),
                "view_item" => __("Voir le event", ""),
                "view_items" => __("Voir les events", ""),
                "search_items" => __("Rechercher un event", "")
            ],
            "description" => "",
            "public" => false,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "show_in_nav_menus" => true,
            "rest_base" => "",
            "has_archive" => true,
            "show_in_menu" => true,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => ["slug" => self::SLUG, "with_front" => false],
            "query_var" => true,
            "menu_icon" => "dashicons-tickets-alt",
            "supports" => ["title", "editor", "thumbnail"],
        ];
    }

    /**
     * Get event as JSON
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            "id" => $this->id(),
            "title" => $this->title(),
            "slug" => $this->slug(),
            "link" => $this->link(),
            "excerpt" => $this->excerpt(),
            "content" => $this->content(),
            "date" => $this->date(),
            "event_dates" => $this->acf('event_dates'),
        ];
    }

    /**
     * Get event categories
     * 
     * @param callable $callback
     */
    public function categories(callable $callback)
    {
        $cat = $this->categories_by_type(EventCategory::TYPE);
        foreach ($cat as $category) {
            $callback(new EventCategory($category));
        }
    }

    public function category_names($separator = ", ", $sanitize = false): string
    {
        $names = array();
        $this->categories(function (EventCategory $category) use (&$names) {
            array_push($names, $category->title());
        });

        if ($sanitize) 
        {
            $names = array_map(function ($name) {
                return sanitize_title($name);
            }, $names);
        }
        return implode($separator, $names);
    }

    /**
     * Get event season
     * 
     * @param callable $callback
     */

    public function season(callable $callback)
    {
        $cat = $this->categories_by_type(EventCategory::SEASON);
        foreach ($cat as $category) {
            return $callback(new EventCategory($category));
        }
    }

    public function season_names($separator = ", ", $sanitize = false): string
    {
        $names = array();
        $this->season(function (EventCategory $category) use (&$names) {
            array_push($names, $category->title());
        });
        if ($sanitize) 
        {
            $names = array_map(function ($name) {
                return sanitize_title("season-" . $name);
            }, $names);
        }
        return implode($separator, $names);
    }

    /**
     * Helper function to compare dates
     * 
     * @param array $date1
     * @param array $date2
     * @return int
     */
    public static function compareDates($date1, $date2)
    {
        return $date1['date'] - $date2['date'];
    }

    /**
     * Get event dates in a range of time
     * 
     * @param int $begin
     * @param int $end
     * @param bool $interval (true = interval, false = single date)
     * @return array
     */
    public function activeDates($begin, $end, bool $interval): array
    {
        $data = $this->acf('event_dates');

        if (!$data)
            return [];
        usort($data, array($this, "compareDates"));
        $active = array();
        foreach ($data as $date) {
            if (
                $date['date'] >= $begin && $date['date'] <= $end && $interval
                && $date['date'] >= time()
            )
                array_push($active, $date['date']);
            if ($date['date'] > time() && !$interval)
                array_push($active, $date['date']);
        }
        if (!$active)
            return [];
        return ($active);
    }

    /**
     * Get event past dates
     * 
     * @return array
     */
    public function archiveDates(): array
    {
        $data = $this->acf('event_dates');
        if (!$data)
            return [];
        usort($data, array($this, "compareDates"));
        $active = array();
        foreach ($data as $date) {
            if ($date['date'] < time())
                array_push($active, $date['date']);
        }
        if (!$active)
            return [];
        // reverse array
        $active = array_reverse($active);
        return ($active);
    }


    /**
     * Is event in the past
     * 
     * @return bool
     */
    public function isPast(): bool
    {
        $data = $this->acf('event_dates');
        if (!$data)
            return false;
        usort($data, array($this, "compareDates"));
        $active = array();
        foreach ($data as $date) {
            if ($date['date'] > time())
                return false;
        }
        return true;
    }

    /**
     * Get dates already passed
     * 
     * @return array
     */
    public static function inactiveDates(): array
    {
        $events = Event::all();
        $data = array();
        // Check if all events event_dates are in the past
        foreach ($events as $event) {
            if ($event->isPast())
                array_push($data, $event);
        }
        // Sort events by date
        usort($data, function ($a, $b) {
            return $a->acf('event_dates')[0]['date'] - $b->acf('event_dates')[0]['date'];
        });
        // add to array by year and set key
        $data = array_reduce($data, function ($carry, $item) {
            $year = date('Y', $item->acf('event_dates')[0]['date']);
            $carry[$year][] = $item;
            return $carry;
        }, []);
        // sort array by year
        krsort($data);
        // reverse year items
        foreach ($data as $year => $events) {
            $data[$year] = array_reverse($events);
        }
        return $data;
    }

    /**
     * Get events by month range
     * 
     * @param int $count
     * @return array
     */
    public static function monthRange(int $count)
    {
        $fmt = datefmt_create('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::FULL, 'Europe/Paris', \IntlDateFormatter::GREGORIAN, 'MMMM');
        $agenda = array();
        $date = new \DateTime();
        $date->modify('first day of this month');
        for ($i = 0; $i < $count; $i++) {
            $agenda[$i] = ucfirst($fmt->format($date));
            $date->modify('first day of next month');
        }
        return ($agenda);
    }

    /**
     * Get next event dates with limit
     * 
     * @param int $number
     * @param callable $callback
     */
    public static function latest($number, callable $callback = null): array
    {
        $models = Event::all();
        $data = array();
        // Check if all events event_dates are in the past
        foreach ($models as $event) {
            if (!$event->isPast())
                array_push($data, $event);
        }

        // Sort events by date
        usort($data, function ($a, $b) {
            return $a->acf('event_dates')[0]['date'] - $b->acf('event_dates')[0]['date'];
        });

        // Get first $number events
        $data = array_slice($data, 0, $number);

        return self::map($data, $callback);
    }

    /**
     * Get all events sorted by date
     */
    public static function sortAll(array $data, int $limite, callable $callback): array
    {
        if (!$data)
            return [];
        $sorted = array();
        foreach ($data as $event) {
            $dates = $event->activeDates(0, 0, false);
            if ($dates)
                array_push($sorted, $event->id());
        }
        // Sort events by date
        usort($sorted, function ($a, $b) {
            $eventA = new Event($a);
            $eventB = new Event($b);
            $datesA = $eventA->activeDates(0, 0, false);
            $datesB = $eventB->activeDates(0, 0, false);
            return $datesA[0] - $datesB[0];
        });
        $sorted = array_unique($sorted);

        if ($limite > 0)
            $sorted = array_slice($sorted, 0, $limite);
        return array_map(function ($id) use ($callback) {
            return $callback(new Event($id));
        }, $sorted);
    }

    /**
     * Get all events sorted by month
     */
    public static function monthlyDates($begin, $end): array
    {
        $montlyevents = array();

        foreach (Event::all() as $event) {
            if ($event->activeDates($begin, $end, true))
                array_push($montlyevents, $event->id());
        }
        if (!$montlyevents)
            return [];
        $sorted = array();
        foreach ($montlyevents as $event) {
            $event = new Event($event);
            $dates = $event->activeDates($begin, $end, true);
            if ($dates)
                array_push($sorted, $event->id());
        }

        // Sort events by date
        usort($sorted, function ($a, $b) {
            $eventA = new Event($a);
            $eventB = new Event($b);
            $datesA = $eventA->activeDates($begin, $end, true);
            $datesB = $eventB->activeDates($begin, $end, true);
            return $datesA[0] - $datesB[0];
        });
        return array_map(function ($id) {
            return new Event($id);
        }, $sorted);
    }

    /**
     * Get all events sorted season
     */
    public static function seasonDates($season = null)
    {
        $season_events = array();
        $events = Event::all();
        $seasons = EventCategory::all_by_type(EventCategory::SEASON);
        
        if ($season) {
            $seasons = array_filter($seasons, function ($cat) use ($season) {
                return $cat->slug() === $season;
            });
        }

        foreach ($seasons as $season) {
            $season_events[$season->slug()] = array();
            foreach ($events as $event) {
                $event_cat = $event->season(function ($category) {
                    return $category;
                });
                if (!$event_cat)
                    continue;
                if ($event_cat->slug() === $season->slug())
                    array_push($season_events[$season->slug()], $event);
            }
        }

        if (!$season_events)
            return [];

        // Sort events by date
        foreach ($season_events as $season => $events) {
            usort($events, function ($a, $b) {
                $datesA = $a->activeDates(0, 0, false);
                $datesB = $b->activeDates(0, 0, false);
                return $datesA[0] - $datesB[0];
            });
            $season_events[$season] = $events;
        }

        // Remove event if has active dates
        foreach ($season_events as $season => $events) {
            $season_events[$season] = array_filter($events, function ($event) {
                return !$event->activeDates(0, 0, false);
            });
        }

        // Reverse key order
        return array_reverse($season_events);
    }

    /**
     * Get all events sorted by alphabetical order
     */
    public function all_alphabetical(callable $callback = null): array
    {
        $models = QueryBuilder::from(static::class)->order('title', 'ASC')->find_all();
        return self::map($models, $callback);
    }
}
