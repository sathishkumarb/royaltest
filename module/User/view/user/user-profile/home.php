<?php
/*
 Template Name: Home
*/
get_header(); ?>

	<div class="banner-outer">
    	<div class="banner">
    
   			 <!-- for the banner, gallery plugin,,, edit the html in nextgen-galley/view/galley.php // i used gallery id number 2-->
			<?php if (function_exists("nggShowGallery"))
				{				
				 echo nggShowGallery(2);
				  } ?>

    	</div>
    </div> 
    <div class="content-container">
    	<div class="testimonial-outer">
    	<?php $pagee = get_page(1); 
    	$img = wp_get_attachment_image_src(get_post_thumbnail_id($pagee->ID),"full");?> 
        	<div class="testimonial-img"><img src="<?php echo $img[0];?>" alt="" /></div>
            <div class="testimonial-text">
            	<h2>Welcome from the <span>Headmaster</span></h2>
                <div class="quote-left">
                	<div class="quote-right">
                	<?php //the_content(); ?>
		            <?php echo $pagee->post_content ?>
                    </div>
                </div>
                <div class="testimonial-name">John Smith <span>Headmaster</span></div>
                
            </div>
            <div class="clear"></div>
        </div>
        <div class="news-container-outer">
        	<div class="news-header">
            	<img src="<?php echo get_template_directory_uri(); ?>/images/news-icon.png" alt="" /> Latest News
            </div>
			<div id="container"></div>            
        </div>
        
        <div class="calendar-container">
        	<div class="news-header">
            	<img src="<?php echo get_template_directory_uri(); ?>/images/calendar-icon.png" alt="" /> Calendar <?php echo date("Y",strtotime("-1 year"));?>/<?php echo date("y")?>
            </div>
            <div class="calendar-outer">
            	<div class="cal-list_head">
                	Dates
                    <div class="calendar-nav"><a href="javascript:;" id="prev">&lt;</a> <a href="javascript:;" id="next">&gt;</a></div>
                </div>
                <div id="displayResults"></div>
                <?php //echo do_shortcode('[events_list_grouped mode="daily" limit="4" date_format="j-n-Y"]  #_EVENTNAME[/events_list_grouped]</span></li>'); ?>
            	<!-- <a class="viewmore" href="<?php echo get_page_link(76); ?>">View More »</a></td> -->
            </div>
            
        </div>
        
        <div class="clear"></div>
    </div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>