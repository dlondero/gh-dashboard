<?php

/**
 * This file is part of gh-dashboard.
 *
 * (c) Daniel Londero <daniel.londero@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function includeIfExists($file)
{
    return file_exists($file) ? include $file : false;
}

if ((!$loader = includeIfExists(__DIR__.'/../vendor/autoload.php')) && (!$loader = includeIfExists(__DIR__.'/../../../autoload.php'))) {
    echo "Cannot find an autoload.php file, have you executed composer install command?" . PHP_EOL;
    exit(1);
}

return $loader;
