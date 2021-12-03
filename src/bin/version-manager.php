<?php
declare(strict_types=1);

namespace Bin;

use Console\Options\Option;
use Console\Options\OptionParser;
use RuntimeException;
use Version\Cli\CliMessageException;
use Version\Cli\SemVer;

if (is_dir('vendor')) {
    require_once 'vendor/autoload.php';
} elseif (is_dir(__DIR__ . '/../vendor')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (is_dir(__DIR__ . '/../../vendor')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} elseif (is_dir(__DIR__ . '/../../../vendor')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
} else {
    throw new RuntimeException("Le dossier vendor n'est pas trouvé");
}

$options = new OptionParser([
    (new Option('init', 'i'))->setType(Option::T_FLAG),
    (new Option('force', 'f'))->setType(Option::T_FLAG),
    (new Option('help', 'h'))->setType(Option::T_FLAG),
    (new Option('path', 'p'))->setType(Option::T_STRING)->setDefault('./version.json'),
    (new Option('increment', 'u'))->setType(Option::T_STRING),
    (new Option('preRelease', 'r'))->setType(Option::T_STRING),
    (new Option('set', 's'))->setType(Option::T_STRING),
]);
$options->parse($argv);

$cliSemVer = new SemVer($options['path']);

$force = (bool)(isset($options['force']) && $options['force']);

try {
    if (isset($options['help']) && $options['help']) {
        echo $cliSemVer->usage();
    } elseif (isset($options['init']) && $options['init']) {
        echo $cliSemVer->init($force);
    } elseif (isset($options['increment'])) {
        echo $cliSemVer->increment($options['increment']);
    } elseif (isset($options['preRelease'])) {
        echo $cliSemVer->preRelease($options['preRelease'], $force);
    } elseif (isset($options['set'])) {
        echo $cliSemVer->set($options['set'], $force);
    } else {
        echo $cliSemVer->get();
    }
} catch (CliMessageException $ex) {
    echo $ex->getMessage();
}
