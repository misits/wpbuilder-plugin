<?php

namespace WPbuilder\views;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

use WPbuilder\models\Options;
?>

<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= __(Options::cbr('crb_maintenance_title'), 'wpbuilder') ?></title>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <main id="single-maintenance">
        <h1><?= __(Options::cbr('crb_maintenance_title'), 'wpbuilder') ?></h1>
        <p><?= __(Options::cbr('crb_maintenance_description'), 'wpbuilder') ?></p>
    </main>
    <?php wp_footer(); ?>
</body>

</html>