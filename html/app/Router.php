<?php

class Router {

    private static $routes = [];

    public static function registerRoute($entry) {

        // register route in routes object
        self::$routes[trim($entry->uri, '/')] = $entry;
    }

    public static function handleRequest($request, $siteVarName = 'SITE') {

        global $$siteVarName;

        if ($request == '') {

            // homepage
            require 'templates/index.php';

        } else if ($request == 'panel') {

            // panel
            require 'app/panel/index.php';

        } else if (array_key_exists($request, self::$routes)) {

            // template
            $entry = self::$routes[$request];
			self::add_trailing_slash();
            require 'templates/' . $entry->template;

        }  else if (file_exists('templates/' . $request . '.php')) {

            // template
            require 'templates/' . $request . '.php';

        } else if (self::file_from_request($request)) {
			
			header('Location: ' .self::file_from_request($request)); 
			exit();

		} else {

            // 404
            if (file_exists('views/404.php')) {
                require 'templates/404.php';
            } else {
                echo '404 not found';
            }
        }
    }

	protected static function file_from_request($request) {

		// parent route exists
		if (self::get_parent_route($request)) {
			$parentUri = self::get_parent_route($request)->uri;
			$relPath = str_replace(ltrim($parentUri, '/'), '', $request);
			$parentPath = self::get_parent_route($request)->path;
			$absPath = '/' . $parentPath . $relPath;
			return $absPath;
		}

		return null;
	}

	protected static function get_parent_route($request) {

		// sort routes by length
		$routes = array_keys(self::$routes);
		usort($routes, function($a, $b) {
			return strlen($b) - strlen($a);
		});

		// find the first route that starts with given request
		foreach ($routes as $route) {
			if (str_starts_with($request, $route)) {
				return self::$routes[$route];
			}
		}

		// none found
		return null;
	}

	protected static function add_trailing_slash() {

		// current URI
		$requestUri = $_SERVER['REQUEST_URI'];

		// ensure the URI doesn't end with '/' or contains '.'
		if (!preg_match('/\/$/', $requestUri) && !pathinfo($requestUri, PATHINFO_EXTENSION)) {

			// Append and redirect
			$newUri = $requestUri . '/';
			header("Location: $newUri", true, 301);
			exit();
		}
	}

}
