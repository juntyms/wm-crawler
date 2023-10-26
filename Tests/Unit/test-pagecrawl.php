<?php
/**
 *  Implements the Unit test set for the Webplan data class.
 *
 * @package     WpmcrawlerPlugin
 * @since       2023
 * @author      JUnn Eric Timoteo
 * @license     GPL-2.0-or-later
 */
namespace ROCKET_WP_CRAWLER;

use WPMedia\PHPUnit\Unit\TestCase;
//use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Mockery;
use WP_Post;

/**
 * Unit test set for the Pagecrawler class.
 */
class Pagecrawl_Test extends TestCase {

	private $instance;

	/**
	 * Method setUp
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		// Instantiate PageCrawler Class.
		$this->instance = new Pages\PageCrawler();
	}

    function test_register()
	{
		$this->instance->register();

		$this->assertNotFalse( has_filter( 'wpmc_links', [ $this->instance, 'wpmc_links' ] ) );

		$this->assertNotFalse( has_action( 'wpmc_delete_links', [ $this->instance, 'wpmc_delete_links' ] ) );

		$this->assertNotFalse( has_action( 'wpmc_crawl_page', [ $this->instance, 'wpmc_crawl_page' ] ));

		$this->assertNotFalse( has_action( 'wpmc_create_sitemap', [ $this->instance, 'wpmc_create_sitemap' ] ));

		$this->assertNotFalse( has_action( 'wpmc_display_links', [ $this->instance, 'wpmc_display_links' ] ));

		$this->assertNotFalse( has_action( 'wpmc_create_file', [ $this->instance, 'wpmc_create_file' ] ) );

		$this->assertNotFalse( has_action( 'wpmc_start_crawl', [ $this->instance, 'wpmc_start_crawl' ] ) );

		$this->assertNotFalse( has_filter( 'wpmc_insert_post', [ $this->instance, 'wpmc_insert_post' ] ) );

	}

	function test_wpmc_start_crawl()
	{
		// Call the register method.
	 	$this->instance->register();

		// Call the start_crawl Method.
		$this->instance->wpmc_start_crawl();

		// Assertions.
		$this->assertSame( 1, did_action('wpmc_delete_links') );

		$this->assertSame( 1, did_action('wpmc_crawl_page') );

		$this->assertSame( 1, did_action('wpmc_create_sitemap') );

		$this->assertTrue( Filters\applied('wpmc_get_full_file_path') > 0 );

		$this->assertSame( 1, did_action('wpmc_create_file') );

	}

	public function test_wpmc_links() {
        // Create a mock instance of plugin class.
        $mock_page_crawler_class = Mockery::mock($this->instance);

        // Define the expected arguments for the wpmc_links method.
        $number_post = -1; // Set your desired number_post value.

        // Expect the wpmc_links method to be called once.
        $mock_page_crawler_class->shouldReceive('wpmc_links')
            ->once()
            ->with($number_post)
			->andReturn('Record Found');

        // Call the function.
        $result = $mock_page_crawler_class->wpmc_links($number_post);

        // Assertion.
		$this->assertSame('Record Found', $result);

    }

	public function test_wpmc_get_url_data()
	{
		$homepage = 'Http://wpmedia.local';

		\Brain\Monkey\Functions\expect( 'get_home_url' )
			// We expect the function to be called once.
			->once()
			// What the function should return when called.
			->andReturn( $homepage );

		\Brain\Monkey\Functions\expect( 'wp_remote_get' )
			->once()
			->with( $homepage )
			->andReturn(function () {
				return [
					'header'=>'this is header',
					'body' => '<html><head><title>My title</title></head><body><p>body</p></body></html>'
				];
			});

		\Brain\Monkey\Functions\expect( 'wp_remote_retrieve_body' )
			->once()
			->andReturn('<html><head><title>My title</title></head><body><p>body</p></body></html>');

		$expected = '<html><head><title>My title</title></head><body><p>body</p></body></html>';

		$result = $this->instance->wpmc_get_url_data();

		$this->assertSame($expected, $result);


	}


}

