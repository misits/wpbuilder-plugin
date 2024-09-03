<?php

namespace WPbuilder\widgets;

// Prevent direct access.
defined('ABSPATH') or exit;

class Matomo extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'matomo_stats_widget', // Base ID
            'Matomo Stats', // Name
            array('description' => __('Displays Matomo Analytics Data.', 'wpbuilder')) // Args
        );


        add_action("admin_enqueue_scripts", function () {
            self::enqueue_matomo_scripts();
        });


        // Dashboard Widget Setup
        add_action('wp_dashboard_setup', array($this, 'add_matomo_dashboard_widgets'));
    }

    public static function enqueue_matomo_scripts() {
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], '3.7.1', true);
        wp_enqueue_script('matomo-widget-script', WPBUILDER_URL . 'admin/assets/js/matomo-chart.min.js', ['chartjs'], null, true);
    }

    public function add_matomo_dashboard_widgets() {
        // Adding Country Stats to the normal (main) column
        wp_add_dashboard_widget(
            'matomo_country_stats', 
            'Country Stats', 
            array($this, 'display_country_data'),
            null, // control callback
            null, // args
            'normal', // context: 'normal', 'side', 'column3', 'column4'
            'high' // priority: 'high', 'core', 'default', 'low'
        );
    
        // Adding Browser Stats to the side column
        wp_add_dashboard_widget(
            'matomo_browser_stats', 
            'Browser Stats', 
            array($this, 'display_browser_data'),
            null, 
            null, 
            'side', // context: placed in the right column
            'high'
        );
    
        // Adding Visits Summary to the normal column, lower priority
        wp_add_dashboard_widget(
            'matomo_visits_summary', 
            'Visits Summary', 
            array($this, 'display_visits_summary'),
            null, 
            null, 
            'column3', 
            'core' // lower than 'high', to place it after Country Stats
        );
    
        // Adding Real-time Visitor Count to the side column, lower priority
        wp_add_dashboard_widget(
            'matomo_realtime_visitor_count', 
            'Real-time Visitor Count', 
            array($this, 'display_realtime_visitor_count'),
            null, 
            null, 
            'column4', // context: placed in the right column
            'core'
        );
    
        // Adding Total Visits to the normal column, even lower priority
        wp_add_dashboard_widget(
            'matomo_total_visits', 
            'Total Visits', 
            array($this, 'display_total_visits'),
            null, 
            null, 
            'column4', 
            'core' // lowest priority to place it last in the normal column
        );
    }

    public function display_country_data() {
        $data = $this->fetch_data('UserCountry.getCountry');
        echo '<canvas id="countryChart" width="400" height="400"></canvas>';
        wp_localize_script('matomo-widget-script', 'countryData', [
            'labels' => array_column($data, 'label'),
            'data' => array_column($data, 'nb_visits')
        ]);
    }
    
    public function display_browser_data() {
        $data = $this->fetch_data('DevicesDetection.getBrowsers');
        echo '<canvas id="browserChart" width="400" height="400"></canvas>';
        wp_localize_script('matomo-widget-script', 'browserData', [
            'labels' => array_column($data, 'label'),
            'data' => array_column($data, 'nb_visits')
        ]);
    }
    
    public function display_visits_summary() {
        $data = $this->fetch_data('VisitsSummary.get');
        echo '<canvas id="visitsSummaryChart" width="400" height="400"></canvas>';
        wp_localize_script('matomo-widget-script', 'visitsSummaryData', [
            'labels' => array_keys($data),
            'data' => array_values($data)
        ]);
    }   
    
    public function display_realtime_visitor_count() {
        $data = $this->fetch_realtime_data();
        echo '<p>Real-time Visitors: ' . (isset($data[0]['visitors']) ? $data[0]['visitors'] : '0') . '</p>';
    }
    
    public function display_total_visits() {
        $data = $this->fetch_visits_summary();
        echo '<p>Total Visits This Month: ' . (isset($data['nb_visits']) ? $data['nb_visits'] : '0') . '</p>';
    }
      

    private function fetch_data($method) {
        $baseUrl = get_option('matomo_url', '');
        $idSite = get_option('matomo_site_id', 0);
        $token_auth = get_option('matomo_api_token', '');
        $commonParams = "idSite=$idSite&period=month&date=today&format=JSON&filter_limit=10&token_auth=$token_auth";

        if (empty($baseUrl) || empty($idSite) || empty($token_auth)) {
            return [];
        }

        $url = $baseUrl . '?' . "module=API&method=$method&" . $commonParams;
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    private function fetch_realtime_data($lastMinutes = 30) {
        $baseUrl = get_option('matomo_url', '');
        $idSite = get_option('matomo_site_id', 0);
        $token_auth = get_option('matomo_api_token', '');
        $url = $baseUrl . "?module=API&method=Live.getCounters&idSite=$idSite&lastMinutes=$lastMinutes&format=JSON&token_auth=$token_auth";
        
        $response = file_get_contents($url);
        return json_decode($response, true);
    }
    
    private function fetch_visits_summary() {
        return $this->fetch_data('VisitsSummary.get');
    }
    
    public static function register_widget()
    {
        register_widget('WPbuilder\widgets\Matomo');
    }
}

// Register the widget using the widgets_init hook
add_action('widgets_init', ['WPbuilder\widgets\Matomo', 'register_widget']);
