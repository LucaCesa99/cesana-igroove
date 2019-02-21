<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__ . '/../vendor/autoload.php';

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->add('', __DIR__ . '/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
}

$loader->add('Zend_', __DIR__ . '/../vendor/zendframework');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

require_once __DIR__ . '/../vendor/adldap/adldap/lib/adLDAP/adLDAP.php';
//require_once __DIR__ . '/../vendor/google/apiclient/src/Google/Service/Directory.php';
//require_once __DIR__ . '/../vendor/google/apiclient/src/Google/Auth/AssertionCredentials.php';


