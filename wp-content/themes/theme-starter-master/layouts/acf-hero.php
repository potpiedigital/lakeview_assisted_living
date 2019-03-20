<?php

// Template for flexible content field
$heading                 = get_sub_field('heading');
$sub_heading             = get_sub_field('sub_heading');
$module_background_color = get_sub_field('module_background_color');
$image_blocks            = get_sub_field('image_blocks');

//you can set classes to a variable and then explode them later. This is useful if you need to also add conditionals for background color on modules etc.
$classes = array('three-col-block', 'forecasts');

if( $module_background_color == 'white' ) :
    $classes[] = 'bg-white';
endif;

if( $module_background_color == 'gray' ) :
    $classes[] = 'bg-lightGray';
endif;

if( $module_background_color == 'lightgray' ) :
    $classes[] = 'bg-extraLightGray';
endif;

//If you uncomment the line below you can print data from fields to see what is getting pulled in. Useful for debugging.
//print_r($sub_heading );

?>

<section class="<?php echo implode(' ', $classes); ?>">

    <div class="container">

        <header class="text-center hasDots">
            <h3><?php echo $sub_heading; ?></h3>
        </header>

        <?php if( $heading ) { ?>

        <div class="text-center">

            <h2 class="big"><?php echo $heading; ?></h2>

        </div>
        
        <?php } ?>

    </div>

    <div class="container">

        <?php $count = 0; foreach ($image_blocks as $key => $data) : $count++;?>

        <div class="span4<?php if ($count == '3') { ?>-last<?php } ?>">

            <figure>
            
                <!-- this is another way to handle responsive images with ACF. -->

                <?php echo wp_get_attachment_image( $data['image']['id'], 'full'); ?>
            
                <?php if( $data['image_content'] ) { ?>

                <figcaption><?php echo $data['image_content']; ?></figcaption>
               
                <?php } ?>

            </figure>
        
        </div>

        <?php endforeach; ?>
    
    </div>
    
</section>
