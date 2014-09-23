Crowdtruth-crowdflower
======================

Extension for the CrowdWatson framework that adds Crowdflower as a platform.

Notice that you must configure the package to provide the API key for your Crowdflower account. In order to configure the package, it is necessary to create your own configuration using the following command:

```
    $ php artisan config:publish crowdtruth/crowdflower
```

Afterwards you should edit file *vendor/crowdtruth/crowdflower/src/config/config.php* as required. In particular, you need to provide your apikey.

For more information visit: http://crowdtruth.org/
