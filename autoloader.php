<?php

/*
 * Turn namespaced class name into path/to/file.php
 */
spl_autoload_register(function($className) {
	$className = str_replace('OrbitalPHP\\', '', $className);
	$className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
	$className = strtolower($className);
	include $className.'.php';
});
