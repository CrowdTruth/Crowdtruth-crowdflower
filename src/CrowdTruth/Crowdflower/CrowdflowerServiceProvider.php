<?php

namespace CrowdTruth\Crowdflower;

use Illuminate\Support\ServiceProvider;
use \Route;

class CrowdflowerServiceProvider extends ServiceProvider {
	
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
	
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {
		$this->package ( 'crowdtruth/crowdflower' );
		$this->app ['CF.retrievejobs'] = $this->app->share ( function () {
			return new RetrieveJobs ();
		} );
		$this->commands ( 'CF.retrievejobs' );
		
		// Register the route to the webhook
		Route::any ( 'cfwebhook.php', function () {
			$cfwebhook = new Cfapi\CFWebhook ();
			$cfwebhook->getSignal ();
		} );
		
		// Important! Bind the shorthand name of the platform. This should be the same as the name in App/config/config.php.
		// $this->app->bind('cf', function()
		// {
		// return new Crowdflower;
		// });
		$this->app->bind ( 'CF', function () {
			return new Crowdflower2 ();
		} );
	}
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
	}
	
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array ();
	}
}
