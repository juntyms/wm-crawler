<?php
/**
 * @package WpmcrawlerPlugin
 */

namespace ROCKET_WP_CRAWLER\Pages;

class PageCrawler {

	public $all_url_post_links;
	public function __construct() {
		$this->all_url_post_links = get_posts(
			array(
				'post_type'   => 'wpmcrawler_links',
				'numberposts' => -1,
			)
		);

		add_action( 'delete_wpmcrawler_links', array( $this, 'delete_wpmcrawler_links' ) );

		add_action( 'insert_data_hook', array( $this, 'insert_data' ) );

		add_action( 'create_sitemap_file', array( $this, 'create_sitemap' ) );

		add_action( 'display_links', array( $this, 'display_all_links' ) );

		add_action( 'crawler_sched_event', array( $this, 'insert_data' ) );
	}

	public function register() {
		do_action( 'insert_data_hook' );
	}

	public function insert_data() {
		do_action( 'create_sitemap_file' );

		do_action( 'delete_wpmcrawler_links' );

		// Get the Homepage URL
		$home_url = get_home_url();

		$urlData = file_get_contents( $home_url );

		$dom = new \DOMDocument();
		@$dom->loadHTML( $urlData );

		$xpath = new \DOMXPath( $dom );
		$hrefs = $xpath->evaluate( '/html/body//a' );

		for ( $i = 0;$i < $hrefs->length;$i++ ) {
			$href = $hrefs->item( $i );

			$url = $href->getAttribute( 'href' );

			$url = filter_var( $url, FILTER_SANITIZE_URL );

			if ( ! filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
				wp_insert_post(
					array(
						'post_title'   => $url,
						'post_content' => $url,
						'post_status'  => 'publish',
						'post_type'    => 'wpmcrawler_links',
					)
				);
			}
		}

		// save homepage to html file
		$homepageFile = fopen( dirname( ROCKET_CRWL_PLUGIN_FILENAME ) . '/src/Files/homepage.html', 'w' ) or die( 'Unable to open file!' );

		fwrite( $homepageFile, $urlData );

		fclose( $homepageFile );
	}

	public function delete_wpmcrawler_links() {
		// Delete all url_links post_type in the database
		foreach ( $this->all_url_post_links as $post_link ) {
			wp_delete_post( $post_link->ID, true );
		}
	}

	public function create_sitemap() {
		$myfile = fopen( dirname( ROCKET_CRWL_PLUGIN_FILENAME ) . '/src/Files/sitemap.html', 'w' ) or die( 'Unable to open file!' );

		$header  = '<html>' . PHP_EOL;
		$header .= '<head>' . PHP_EOL;
		$header .= '<title>Sitemap</title>' . PHP_EOL;
		$header .= '</head>' . PHP_EOL;
		$header .= '<body>' . PHP_EOL;

		fwrite( $myfile, $header );

		foreach ( $this->all_url_post_links as $post_link ) {

			fwrite( $myfile, '<p>' . $post_link->post_content . "</p>\n" );
		}

		$footer  = '</body>' . PHP_EOL;
		$footer .= '</html>' . PHP_EOL;

		fwrite( $myfile, $footer );

		fclose( $myfile );
	}

	public function display_all_links() {
		echo '<div class="wrap">';
		echo '<div class="card">';
		foreach ( $this->all_url_post_links as $post_link ) {
			echo '<p>' . $post_link->post_content . '</p>';
		}
		echo '</div>';
		echo '</div>';
	}
}


// ! Do homepage crawling -done

// ! Delete the result from the lst crwal - done

// ! Delete the sitemap.html if exist

// ! Extract all of the internal hyperlinks present in the homepage - done

// ! store results in database - done

// ! display the results on the admin page

// ! save the homepage as .html in the server

// ! set crawl to run automatically every hour

// error occurred inform error
