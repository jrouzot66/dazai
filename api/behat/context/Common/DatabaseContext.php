<?php

namespace App\Behat\Common;

use Behat\Behat\Context\Context;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Symfony\Component\Process\Process;

class DatabaseContext implements Context
{
    /**
     * @BeforeSuite
     */
    public static function beforeSuite(BeforeSuiteScope $scope): void
    {
        self::run(['php', 'bin/console', 'doctrine:database:create', '--if-not-exists', '--env=test']);
        self::run(['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction', '--env=test']);
    }

    private static function run(array $cmd): void
    {
        $p = new Process($cmd);
        $p->setTimeout(300);
        $p->run();

        if (!$p->isSuccessful()) {
            throw new \RuntimeException(sprintf(
                "Command failed: %s\n\n%s",
                implode(' ', $cmd),
                $p->getErrorOutput() ?: $p->getOutput()
            ));
        }
    }
}