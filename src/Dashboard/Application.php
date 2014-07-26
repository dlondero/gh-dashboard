<?php

/**
 * This file is part of gh-dashboard.
 *
 * (c) Daniel Londero <daniel.londero@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gh\Dashboard;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;

class Application extends ConsoleApplication
{
    /**
     * @param InputInterface $input The input interface
     * @return string
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'gh-dashboard';
    }

    /**
     * @return array
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new Dashboard();

        return $defaultCommands;
    }

    /**
     * @return InputDefinition
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
