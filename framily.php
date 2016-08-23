<?php

require_once "vendor/autoload.php";

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Resolver\URLResolver;
use Resolver\URLResolverResult;
use Bramus\Monolog\Formatter\ColoredLineFormatter;

$log = new Logger('redirect');
$handler = new StreamHandler('php://stdout');
$handler->setFormatter(new ColoredLineFormatter(null, "[%datetime%] %level_name%: %message%\n"));
$log->pushHandler($handler);

$resolver = new URLResolver();
$urlList = require_once "urlList.php";

$resolver->setMaxRedirects(5);

foreach ($urlList as $oldUrl => $expectedRedirectUrl) {
    /** @var URLResolverResult $resolverResult */
    $resolverResult = $resolver->resolveURL($oldUrl);

    if ($resolverResult->getURL() !== $expectedRedirectUrl) {
        $log->addError(sprintf(
            "%s doesn't resolve to %s but to %s",
            $oldUrl,
            $expectedRedirectUrl,
            $resolverResult->getURL()
        ));
    } else {
        $log->addInfo(sprintf(
            "%s resolves correctly to %s",
            $oldUrl,
            $expectedRedirectUrl
        ));
    }
}