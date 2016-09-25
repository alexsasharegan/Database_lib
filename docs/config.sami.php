<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 9/25/16
 * Time: 7:50 AM
 */
use Sami\Sami;
use Symfony\Component\Finder\Finder;

require_once __DIR__ . "/../vendor/autoload.php";

$iterator = Finder::create()
                  ->files()
                  ->name( '*.php' )
                  ->in( __DIR__ . '/../src' );

$sami_options = [
	// 'theme'                => 'symfony',
	'title'                => 'Database\MySQL API',
	'build_dir'            => __DIR__ . '/build',
	'cache_dir'            => __DIR__ . '/cache',
	'default_opened_level' => 2,
];

return new Sami( $iterator, $sami_options );