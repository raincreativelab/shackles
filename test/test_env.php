<?php
use Symfony\Component\ClassLoader\Psr4ClassLoader;

$base = dirname(__DIR__);

require_once $base."/vendor/symfony/class-loader/Symfony/Component/ClassLoader/Psr4ClassLoader.php";
require_once $base."/vendor/autoload.php";

$loader = new Psr4ClassLoader();
$loader->addPrefix("shackles", $base . "/lib");
$loader->register();