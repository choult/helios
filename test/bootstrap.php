<?php
//require_once $_SERVER['SYMFONY'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/../../symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

// load namespaces
$loader->registerNamespaces( array(
    'Symfony'   => __DIR__.'/../../symfony/src',
    'Helios'   => __DIR__.'/../lib',
    ) );

print __DIR__.'/../lib' . "\n";

// Prefix Non-name spaced classe
$loader->registerPrefixes( array(
   'Apache_'    => __DIR__.'/../lib/vendor/SolrPhpClient',
   'sfYaml'     => __DIR__.'/../lib/vendor/sfYaml/lib',
) );

$loader->register();