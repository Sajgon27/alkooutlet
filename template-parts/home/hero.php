<?php
/**
 * Template part for displaying the hero section on the front page
 *
 * @package alkooutlet
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get ACF image fields from hero group
$hero = get_field('hero');
$image_1 = $hero['zdjecie_1'] ?? null;
$image_2 = $hero['zdjecie_2'] ?? null;
$image_3 = $hero['zdjecie_3'] ?? null;
$image_4 = $hero['zdjecie_4'] ?? null;
$image_5 = $hero['zdjecie_5'] ?? null;
$image_6 = $hero['zdjecie_6'] ?? null;
?>

<section class="hero">
    <div class="container">
        <div class="hero__grid">
            <?php if($image_1): ?>
                <div class="hero__item hero__item--1">
                    <?php echo wp_get_attachment_image($image_1, 'full', false, array('class' => 'hero__image')); ?>
                </div>
            <?php endif; ?>
            
            <?php if($image_2): ?>
                <div class="hero__item hero__item--2">
                    <?php echo wp_get_attachment_image($image_2, 'full', false, array('class' => 'hero__image')); ?>
                </div>
            <?php endif; ?>
            
            <?php if($image_3): ?>
                <div class="hero__item hero__item--3">
                    <?php echo wp_get_attachment_image($image_3, 'full', false, array('class' => 'hero__image')); ?>
                </div>
            <?php endif; ?>
            
            <?php if($image_4): ?>
                <div class="hero__item hero__item--4">
                    <?php echo wp_get_attachment_image($image_4, 'full', false, array('class' => 'hero__image')); ?>
                </div>
            <?php endif; ?>
            
            <?php if($image_5): ?>
                <div class="hero__item hero__item--5">
                    <?php echo wp_get_attachment_image($image_5, 'full', false, array('class' => 'hero__image')); ?>
                </div>
            <?php endif; ?>
            
            <?php if($image_6): ?>
                <div class="hero__item hero__item--6">
                    <?php echo wp_get_attachment_image($image_6, 'full', false, array('class' => 'hero__image')); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
