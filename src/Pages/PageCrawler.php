<?php
/**
 * Plugin main class
 *
 * @package     WpmcrawlerPlugin
 * @since       2023
 * @author      Junn Eric Timoteo
 * @license     GPL-2.0-or-later
 */

namespace ROCKET_WP_CRAWLER\Pages;

/**
 * PageCrawler
 */
class PageCrawler {

	/**
	 * Public Variable all_url_post_links
	 *
	 * @var mixed
	 */
	public $all_url_post_links;

	/**
	 * Method __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->all_url_post_links = get_posts(
			array(
				'post_type'   => 'wpmcrawler_links',
				'numberposts' => -1,
			)
		);

		add_action( 'wpmc_delete_links', array( $this, 'wpmc_delete_links' ) );

		add_action( 'wpmc_insert_data_hook', array( $this, 'insert_data' ) );

		add_action( 'wpmc_create_sitemap_file', array( $this, 'create_sitemap' ) );

		add_action( 'wpmc_display_links', array( $this, 'wpmc_display_links' ) );
	}

	/**
	 * Method register
	 *
	 * @return void
	 */
	public function register() {
		do_action( 'wpmc_insert_data_hook' );
	}

	/**
	 * Method insert_data
	 *
	 * @return void
	 */
	public function insert_data() {
		do_action( 'wpmc_create_sitemap_file' );

		do_action( 'wpmc_delete_links' );

		// Get the Homepage URL.
		$home_url = get_home_url();

		$url_data = wp_remote_get( $home_url );

		$url_data = (string) $url_data['body'];

		$dom = new \DOMDocument();
		@$dom->loadHTML( $url_data );

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

		// save homepage to html file.
		$homepage_file = fopen( dirname( ROCKET_CRWL_PLUGIN_FILENAME ) . '/src/Files/homepage.html', 'w' ) or die( 'Unable to open file!' );

		fwrite( $homepage_file, $url_data );

		fclose( $homepage_file );
	}

	/**
	 * Method wpmc_delete_links
	 *
	 * @return void
	 */
	public function wpmc_delete_links() {
		// Delete all url_links post_type in the database.
		foreach ( $this->all_url_post_links as $post_link ) {
			wp_delete_post( $post_link->ID, true );
		}
	}

	/**
	 * Method create_sitemap
	 *
	 * @return void
	 */
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

	/**
	 * Method wpmc_display_links.
	 */
	public function wpmc_display_links() {
		echo '<div class="wrap">';
		echo '<div class="card">';
		foreach ( $this->all_url_post_links as $post_link ) {
			echo '<p>' . esc_html( $post_link->post_content ) . '</p>';
		}
		echo '</div>';
		echo '</div>';
	}
}
