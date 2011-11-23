<?php
/*
Plugin Name: Related Tags
Plugin URI: http://geektastical.com
Description: Suggests related posts based on tags
Version: 0.6
Author: A3
Author URI: http://geektastical.com
License: Copyright 2011 Geektastical.com
*/

add_filter( "the_content", "geektastical_add_related_links_to_post", 100 );
add_action('wp_print_styles', 'geektastical_add_stylesheet');

function geektastical_add_stylesheet()
{
	$myStyleUrl = WP_PLUGIN_URL . '/related_tags/style.css';
	wp_register_style('geektastical_related_tags_style', $myStyleUrl);
	wp_enqueue_style( 'geektastical_related_tags_style');
}

function geektastical_add_related_links_to_post( $content )
{	
	$strategy = new geektastical_SharedTagsStrategy();
	$related_links = new geektastical_RelatedImageLinksModule( $strategy );
	
	if ( $related_links->generate_related_posts() )
		$content .= $related_links->to_html();
	
	return $content;	
}

# Books
# Cooking
# Geeking Out
# Photography			
# Rants & Ravings
# Technology 

class geektastical_RelatedImageLinksModule 
{
	private $related_strategy;
	private $posts;
	
	private $default_images = array('technology.png', 'technology.png', 'technology.png', 'technology.png', 'technology.png');
	
	function __construct( $strategy ) 
	{
		$this->related_strategy = $strategy;
	}
	
	public function generate_related_posts()
	{
		$this->posts = $this->related_strategy->get_related_posts();
		return ( count($this->posts) > 0 );
	}
		
	public function to_html()
	{
		$links = $this->links_html();
		$header = $this->related_strategy->get_header();
		
		$html = <<<HTML
<div class='related_section'/>
<h5 class='related_header'>$header</h5>

$links	

</div>		
HTML;

		return $html;
	}
	
	private function links_html()
	{
		$html = "";
		
		$images = $this->default_images;
		shuffle($images);
		

		
		foreach( $this->posts as $p ) 
		{
			if ( has_post_thumbnail($p->ID)) 
			{
			    $image = get_the_post_thumbnail($p->ID, 'thumbnail');
			}
			else
			{
				$image_file = WP_PLUGIN_URL . '/related_tags/images/'. array_shift($images); 
				
				$image = "<img src='$image_file' title='$p->post_title' class='related_img'></img>";
			}
			
			$link = get_permalink($p->ID);
			$html .= "
<div class='related_article'>
$image
<a href='$link' class='related_link'>$p->post_title</a>
</div>";	
		
		}
		
		return $html;
	}

}

# Returns a random set of posts sharing tags with the current post 
class geektastical_SharedTagsStrategy
{
	public function get_related_posts()
	{
		global $post;
		
		$tag_ids = array();	
	
		$tags = get_the_tags();
		if ( is_array ($tags) )
		{
			foreach( $tags as $t ) 
			{
				array_push ($tag_ids, (int) $t->term_id);
			}
			
			$args = array( 'numberposts' => 4, 'tag__in' => $tag_ids, 'orderby' => 'rand', 'exclude' => $post->ID );

			return get_posts( $args );
		}
		
		return array();
	}
	
	public function get_header()
	{
		return "Related articles:";
	}
	
}





?>
