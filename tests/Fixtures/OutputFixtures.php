<?php

/**
 * This file is part of gh-dashboard.
 *
 * (c) Daniel Londero <daniel.londero@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gh\Tests\Fixtures;

class OutputFixtures
{

    const ISSUE_LIST = <<<EOT
[foo/bar]
    Found a bug -> https://github.com/foo/bar/issues/1347
EOT;

    const NO_ISSUES = <<<EOT
w00t! Seems like you're done with all issues. Or maybe your params are wrong?
EOT;

}
