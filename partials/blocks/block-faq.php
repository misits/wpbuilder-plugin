<?php

namespace WPbuilder\partials\blocks;


// Prevent direct access.
defined('ABSPATH') or exit;

use WPbuilder\models\custom\Faq;
?>

<div class="faqs">
    <div class="component-wrapper">
        <h3><?php echo esc_html(__('Frequently Asked Questions', 'wpbuilder')); ?></h3>
        <div class="faqs-wrapper">
            <?php foreach ($fields->crb_faq as $faq) : ?>
                <?php $model = new Faq($faq->id) ?>
                <?php foreach ($model->crb('crb_faq_questions') as $item) : ?>
                    <div class="faq">
                        <div class="faq__title">
                            <span><?php echo esc_html(__($item['crb_question'], WPBUILDER_DOMAIN)) ?></span>
                            <svg viewbox="0 0 24 24">
                                <path class="iconV" d="M 12,0 V 24" />
                                <path class="iconH" d="M 0,12 H 24" />
                            </svg>
                        </div>
                        <div class="faq__answer">
                            <div class="faq__answer-text">
                                <p><?php echo esc_html(__($item['crb_answer'], WPBUILDER_DOMAIN)) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>