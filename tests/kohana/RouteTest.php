<?php

/**
 * Description of RouteTest
 *
 * @group kohana
 *
 * @package    Unittest
 * @author     Kohana Team
 * @author     BRMatt <matthew@sigswitch.com>
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_RouteTest extends Kohana_Unittest_TestCase
{
	/**
	 * Route::matches() should return false if the route doesn't match against a uri
	 *
	 * @test
	 */
	function test_matches_returns_false_on_failure()
	{
		$route = new Route('projects/(<project_id>/(<controller>(/<action>(/<id>))))');

		$this->assertSame(FALSE, $route->matches('apple/pie'));
	}

	/**
	 * Route::matches() should return an array of parameters when a match is made
	 * An parameters that are not matched should not be present in the array of matches
	 * 
	 * @test
	 * @covers Route
	 */
	function test_matches_returns_array_of_parameters_on_successful_match()
	{
		$route = new Route('(<controller>(/<action>(/<id>)))');
		
		$matches = $route->matches('welcome/index');

		$this->assertType('array', $matches);
		$this->assertArrayHasKey('controller', $matches);
		$this->assertArrayHasKey('action', $matches);
		$this->assertArrayNotHasKey('id', $matches);
		$this->assertSame(2, count($matches));
		$this->assertSame('welcome', $matches['controller']);
		$this->assertSame('index', $matches['action']);
	}

	/**
	 * Defaults specified with defaults() should be used if their values aren't
	 * present in the uri
	 *
	 * @test
	 * @covers Route
	 */
	function test_defaults_are_used_if_params_arent_specified()
	{
		$route = new Route('(<controller>(/<action>(/<id>)))');
		$route->defaults(array('controller' => 'welcome', 'action' => 'index'));

		$matches = $route->matches('');

		$this->assertType('array', $matches);
		$this->assertArrayHasKey('controller', $matches);
		$this->assertArrayHasKey('action', $matches);
		$this->assertArrayNotHasKey('id', $matches);
		$this->assertSame(2, count($matches));
		$this->assertSame('welcome', $matches['controller']);
		$this->assertSame('index', $matches['action']);
	}

	/**
	 * This tests that routes with required parameters will not match uris without them present
	 * 
	 * @test
	 * @covers Route
	 */
	function test_required_parameters_are_needed()
	{
		$route = new Route('admin(/<controller>(/<action>(/<id>)))');

		$this->assertFalse($route->matches(''));

		$matches = $route->matches('admin');

		$this->assertType('array', $matches);

		$matches = $route->matches('admin/users/add');

		$this->assertType('array', $matches);
		$this->assertSame(2, count($matches));
		$this->assertArrayHasKey('controller', $matches);
		$this->assertArrayHasKey('action', $matches);
	}

	/**
	 * This tests the reverse routing returns the uri specified in the route
	 * if it's a static route
	 *
	 * A static route is a route without any parameters
	 *
	 * @test
	 * @covers Route::uri
	 */
	function test_reverse_routing_returns_routes_uri_if_route_is_static()
	{
		$route = new Route('info/about_us');

		$this->assertSame('info/about_us', $route->uri(array('some' => 'random', 'params' => 'to confuse')));
	}

	/**
	 * Tests route caching
	 * 
	 * @todo remove the cached files. need to delete the cache before running the unit tests to get coverage
	 *
	 * @test
	 * @covers Route::cache
	 */
	function test_cache_name()
	{
		$this->assertSame(FALSE, Route::cache());
		Route::set('foobar', '(<controller>(/<action>(/<id>)))')
			->defaults(array(
				'controller' => 'welcome',
			)
		);

		$route = Route::get('foobar');
		$this->assertSame('foobar', Route::name($route)); // Why doesn't this give code coverage to name() ?
		$this->assertSame(NULL, Route::cache(TRUE));
	}
}