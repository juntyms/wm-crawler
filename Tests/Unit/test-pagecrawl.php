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

    function test_register()
	{
		$page_crawler_class = new Pages\PageCrawler();

		$page_crawler_class->register();

		self::assertNotFalse( has_filter( 'wpmc_links', [ $page_crawler_class, 'wpmc_links' ] ) );

		self::assertNotFalse( has_action( 'wpmc_delete_links', [ $page_crawler_class, 'wpmc_delete_links' ] ) );

		self::assertNotFalse( has_action( 'wpmc_crawl_page', [ $page_crawler_class, 'wpmc_crawl_page' ] ));

		self::assertNotFalse( has_action( 'wpmc_create_sitemap', [ $page_crawler_class, 'wpmc_create_sitemap' ] ));

		self::assertNotFalse( has_action( 'wpmc_display_links', [ $page_crawler_class, 'wpmc_display_links' ] ));

		self::assertNotFalse( has_action( 'wpmc_create_file', [ $page_crawler_class, 'wpmc_create_file' ] ) );

		self::assertNotFalse( has_action( 'wpmc_start_crawl', [ $page_crawler_class, 'wpmc_start_crawl' ] ) );

		self::assertNotFalse( has_filter( 'wpmc_insert_post', [ $page_crawler_class, 'wpmc_insert_post' ] ) );

	}

	function test_wpmc_start_crawl()
	{
		// Instantiate Class.
	 	$page_crawler_class = new Pages\PageCrawler();

		// Call the register method.
	 	$page_crawler_class->register();

		// Call the start_crawl Method.
		$page_crawler_class->wpmc_start_crawl();

		// Assertions.
		$this->assertSame( 1, did_action('wpmc_delete_links') );

		$this->assertSame( 1, did_action('wpmc_crawl_page') );

		$this->assertSame( 1, did_action('wpmc_create_sitemap') );

		$this->assertTrue( Filters\applied('wpmc_get_full_file_path') > 0 );

		$this->assertSame( 1, did_action('wpmc_create_file') );

	}


	public function test_wpmc_links() {
        // Create a mock instance of plugin class.
        $mock_page_crawler_class = Mockery::mock(new Pages\PageCrawler());

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
}

