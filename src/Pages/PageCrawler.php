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
	 * Method register
	 */
	public function register() {

		add_filter( 'wpmc_links', array( $this, 'wpmc_links' ) );

		add_action( 'wpmc_delete_links', array( $this, 'wpmc_delete_links' ) );

		add_action( 'wpmc_crawl_page', array( $this, 'wpmc_crawl_page' ) );

		add_action( 'wpmc_create_sitemap', array( $this, 'wpmc_create_sitemap' ) );

		add_action( 'wpmc_display_links', array( $this, 'wpmc_display_links' ) );

		add_action( 'wpmc_create_file', array( $this, 'wpmc_create_file' ), 10, 2 );

		add_action( 'wpmc_start_crawl', array( $this, 'wpmc_start_crawl' ) );

		add_filter( 'wpmc_insert_post', array( $this, 'wpmc_insert_post' ) );

		add_filter( 'wpmc_get_url_data', array( $this, 'wpmc_get_url_data' ) );

		add_filter( 'wpmc_get_full_file_path', array( $this, 'wpmc_get_full_file_path' ) );
	}

	/**
	 * Method wpmc_get_url_data
	 *
	 * @param $url_data $url_data the home url.
	 *
	 * @return $url_data
	 */
	public function wpmc_get_url_data( $url_data = null ) {
		// Get the Homepage URL.
		$home_url = get_home_url();

		// Get the content of the homepage.
		$url_home_data = wp_remote_get( $home_url );

		// Retrieve the body of the homepage.
		$url_data = wp_remote_retrieve_body( $url_home_data );

		// Return the string body of homepage.
		return $url_data;
	}

	/**
	 * Method wpmc_start_crawl
	 *
	 * @return void
	 */
	public function wpmc_start_crawl() {

		// Delete the links found.
		do_action( 'wpmc_delete_links' );

		$url_data = apply_filters( 'wpmc_get_url_data', array() );

		do_action( 'wpmc_crawl_page', $url_data );

		// Prepare the links to be added to sitemap file.
		do_action( 'wpmc_create_sitemap' );

		$filename = apply_filters( 'wpmc_get_full_file_path', 'homepage.html' );

		// Call the wpmc_create_file hook to  generate the file based on the filename and content.
		do_action( 'wpmc_create_file', $url_data, $filename );
	}

	/**
	 * Method wpmc_get_full_file_path
	 *
	 * @param $filename $filename filename to generate the full path.
	 *
	 * @return $filename
	 */
	public function wpmc_get_full_file_path( $filename ) {

		$filename = (string) dirname( ROCKET_CRWL_PLUGIN_FILENAME ) . '/src/Files/' . $filename;

		return $filename;
	}

	/**
	 * Method wpmc_links
	 *
	 * @param $number_post $number_post number of post to return.
	 *
	 * @return $links
	 */
	public function wpmc_links( $number_post ) {

		$links = new \WP_Query(
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
	 * @return void
	 */
	public function wpmc_delete_links() {

		$links = apply_filters( 'wpmc_links', -1 );

		if ( $links->have_posts() ) {

			while ( $links->have_posts() ) {
				$links->the_post();
				wp_delete_post( get_the_ID() );
			}
		}

		wp_reset_postdata();
	}


	/**
	 * Method wpmc_create_sitemap
	 *
	 * @return void
	 */
	public function wpmc_create_sitemap() {

		// Get the newly added links from the database.
		$links = apply_filters( 'wpmc_links', -1 );

		$content  = '<html>' . PHP_EOL;
		$content .= '<head>' . PHP_EOL;
		$content .= '<title>Sitemap</title>' . PHP_EOL;
		$content .= '</head>' . PHP_EOL;
		$content .= '<body>' . PHP_EOL;

		if ( $links->have_posts() ) {
			while ( $links->have_posts() ) {
				$links->the_post();
				$post_content = get_the_content();
				$content     .= '<p>' . esc_html( $post_content ) . '</p>' . PHP_EOL;
			}
			wp_reset_postdata();
		}

		$content .= '</body>' . PHP_EOL . '</html>' . PHP_EOL;

		$filename = apply_filters( 'wpmc_get_full_file_path', 'sitemap.html' );

		do_action( 'wpmc_create_file', $content, $filename );
	}

	/**
	 * Method wpmc_display_links.
	 */
	public function wpmc_display_links() {

		$wpmc_error = new \WP_Error();

		$links = apply_filters( 'wpmc_links', -1 );

		echo '<div class="wrap">';
		echo '<div class="card">';

		if ( $links->have_posts() ) {
			while ( $links->have_posts() ) {
				$links->the_post();
				echo '<p>' . esc_html( get_the_content() ) . '</p>';
			}
			wp_reset_postdata();
		} else {
			$wpmc_error->add( 'No Links', 'There is/are no link/s found on you website homepage' );

			echo esc_html( $wpmc_error->get_error_message() );
		}

		echo '</div>';
		echo '</div>';
	}
}
