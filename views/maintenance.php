<?php

namespace WPbuilder\views;

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

?>

<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= __('Site en maintenance', 'wpbuilder') ?></title>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <main id="maintenance">
        <h1><?= __('Site en maintenance', 'wpbuilder') ?></h1>
        <p><?= __('Nous sommes en train de mettre à jour le site, nous revenons très vite !', 'wpbuilder') ?></p>
    </main>
    <?php wp_footer(); ?>
</body>

</html>