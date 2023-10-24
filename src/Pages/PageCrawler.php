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
	 * Method __construct
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'wpmc_wpc_links', array( $this, 'wpmc_wpc_links' ) );

		add_action( 'wpmc_delete_links', array( $this, 'wpmc_delete_links' ) );

		add_action( 'wpmc_crawl_page', array( $this, 'wpmc_crawl_page' ) );

		add_action( 'wpmc_create_sitemap', array( $this, 'wpmc_create_sitemap' ) );

		add_action( 'wpmc_display_links', array( $this, 'wpmc_display_links' ) );

		add_action( 'wpmc_create_file', array( $this, 'wpmc_create_file' ), 10, 2 );

		add_action( 'wpmc_start_crawl', array( $this, 'wpmc_start_crawl' ) );

		add_filter( 'wpmc_insert_post', array( $this, 'wpmc_insert_post' ) );
	}

	/**
	 * Method register
	 */
	public function register() {

		do_action( 'wpmc_start_crawl' );
	}


	/**
	 * Method wpmc_start_crawl
	 *
	 * @return void
	 */
	public function wpmc_start_crawl() {

		$links = apply_filters( 'wpmc_wpc_links', -1 );

		do_action( 'wpmc_delete_links', $links );

		// Get the Homepage URL.
		$home_url = get_home_url();

		// Get the content of the homepage.
		$url_data = wp_remote_get( $home_url );

		// Convert to the content to string.
		$url_data = (string) $url_data['body'];

		do_action( 'wpmc_crawl_page', $url_data );

		// Get the newly added links from the database.
		$links = apply_filters( 'wpmc_wpc_links', -1 );

		// Prepare the links to be added to sitemap file.
		do_action( 'wpmc_create_sitemap', $links );

		$filename = (string) dirname( ROCKET_CRWL_PLUGIN_FILENAME ) . '/src/Files/homepage.html';

		$content = (string) $url_data;

		do_action( 'wpmc_create_file', $content, $filename );
	}


	/**
	 * Method wpmc_wpc_links
	 *
	 * @param $number_post $number_post number of post to return.
	 *
	 * @return $links
	 */
	public function wpmc_wpc_links( $number_post ) {

		$links = get_posts(
			array(
				'post_type'   => 'wpmcrawler_links',
				'numberposts' => $number_post,
			)
		);

		return $links;
	}

	/**
	 * Method wpmc_insert_post
	 *
	 * @param $url $url the link URL.
	 *
	 * @return void
	 */
	public function wpmc_insert_post( $url ) {

		wp_insert_post(
			array(
				'post_title'   => $url,
				'post_content' => $url,
				'post_status'  => 'publish',
				'post_type'    => 'wpmcrawler_links',
			)
		);
	}


	/**
	 * Method wpmc_crawl_page
	 *
	 * @param $url_data $url_data the page content.
	 *
	 * @return void
	 */
	public function wpmc_crawl_page( $url_data ) {

		$dom = new \DOMDocument();
		@$dom->loadHTML( $url_data );

		$xpath = new \DOMXPath( $dom );
		$hrefs = $xpath->evaluate( '/html/body//a' );

		for ( $i = 0;$i < $hrefs->length;$i++ ) {
			$href = $hrefs->item( $i );

			$url = $href->getAttribute( 'href' );

			$url = filter_var( $url, FILTER_SANITIZE_URL );

			if ( ! filter_var( $url, FILTER_VALIDATE_URL ) === false ) {

				apply_filters( 'wpmc_insert_post', $url );

			}
		}
	}

	/**
	 * Method wpmc_create_file
	 *
	 * @param $content  $content this is the content of the file.
	 * @param $filename $filename the full path of the filename.
	 */
	public function wpmc_create_file( $content, $filename ) {

		$access_type = get_filesystem_method();

		if ( 'direct' === $access_type ) {
			/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
			$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );
			/* initialize the API */
			if ( ! WP_Filesystem( $creds ) ) {
				/* any problems and we exit */
				return false;
			}
			global $wp_filesystem;

			$wp_filesystem->put_contents(
				$filename,
				$content,
				FS_CHMOD_FILE // predefined mode settings for WP files.
			);
		}
	}


	/**
	 * Method wpmc_delete_links
	 *
	 * @param $all_url_post_links $all_url_post_links query of custom post links.
	 *
	 * @return void
	 */
	public function wpmc_delete_links( $all_url_post_links ) {
		// Delete all url_links post_type in the database.
		foreach ( $all_url_post_links as $post_link ) {
			wp_delete_post( $post_link->ID, true );
		}
	}


	/**
	 * Method wpmc_create_sitemap
	 *
	 * @param $all_url_post_links $all_url_post_links query of custom post links.
	 *
	 * @return void
	 */
	public function wpmc_create_sitemap( $all_url_post_links ) {

		$content  = '<html>' . PHP_EOL;
		$content .= '<head>' . PHP_EOL;
		$content .= '<title>Sitemap</title>' . PHP_EOL;
		$content .= '</head>' . PHP_EOL;
		$content .= '<body>' . PHP_EOL;

		foreach ( $all_url_post_links as $post_link ) {

			$content .= "<p>{$post_link->post_content}</p>\n";
		}

		$content .= '</body>' . PHP_EOL . '</html>' . PHP_EOL;

		$filename = (string) dirname( ROCKET_CRWL_PLUGIN_FILENAME ) . '/src/Files/sitemap.html';

		do_action( 'wpmc_create_file', $content, $filename );
	}

	/**
	 * Method wpmc_display_links.
	 */
	public function wpmc_display_links() {

		$links = apply_filters( 'wpmc_wpc_links', -1 );

		echo '<div class="wrap">';
		echo '<div class="card">';
		foreach ( $links as $post_link ) {
			echo '<p>' . esc_html( $post_link->post_content ) . '</p>';
		}
		echo '</div>';
		echo '</div>';
	}
}
