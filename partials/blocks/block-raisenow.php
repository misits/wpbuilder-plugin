<?php

namespace WPbuilder\partials\blocks;

?>

<?php if (!empty($fields->crb_widget_id)) : ?>
    <div class="rnw-widget-container"></div>
    <script src="https://tamaro.raisenow.com/<?php echo esc_attr($fields->crb_widget_id) ?>/latest/widget.js"></script>
    <script>
        window.rnw.tamaro.runWidget('.rnw-widget-container')
    </script>
<?php endif; ?>