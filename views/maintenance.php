<?php

namespace WPbuilder\views;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use WPbuilder\models\custom\Option;
use function WPbuilder\render_partial;
?>

<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= __(Option::crb('crb_maintenance_title'), 'wpbuilder') ?></title>

     <!-- Favicon -->
     <?= render_partial("head/favicon", [
        "color" => "#ffffff",
    ]); ?>

    <!-- JSON-LD -->
    <?= render_partial("head/jsonld"); ?>

    <!-- JavaScript -->
    <script>
        window.baseUrl = "<?= get_home_url(); ?>";
        window.appName = "<?= sanitize_title(get_bloginfo('name')); ?>";
        window.apiUrl = "<?= get_rest_url(); ?>";
    </script>

    <!-- CSS -->
    <style>
        body {
            opacity: 0;
            transition: opacity 2s;
        }
    </style>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>  <?php body_class(); ?> onload="document.body.style.opacity='1'">
    <main id="single-maintenance">
        <?= render_partial("layout/maintenance"); ?>
    </main>
    <?php wp_footer(); ?>
</body>

</html>