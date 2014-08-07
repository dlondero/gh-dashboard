<?php

/**
 * This file is part of gh-dashboard.
 *
 * (c) Daniel Londero <daniel.londero@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gh\Tests\Dashboard;

use Gh\Dashboard\Dashboard;
use Gh\Dashboard\Application;
use Gh\Tests\Fixtures\OutputFixtures;
use Symfony\Component\Console\Tester\CommandTester;

class DashboardTest extends \PHPUnit_Framework_TestCase
{
    public function testOutputDefault()
    {
        $dashboardMock = $this->getMock('Gh\Dashboard\Dashboard', array('getConfig', 'getIssues'));
        $dashboardMock->expects($this->once())->method('getConfig')->willReturn($this->getConfig());
        $dashboardMock->expects($this->once())->method('getIssues')->with(
            $this->equalTo('12345abcde'),
            $this->equalTo('foo'),
            $this->equalTo('mentioned'),
            $this->equalTo('open')
        )->willReturn($this->getIssues());

        $application = new Application();
        $dashboardMock->setApplication($application);

        $commandTester = new CommandTester($dashboardMock);
        $commandTester->execute(array());

        $this->assertEquals(OutputFixtures::ISSUE_LIST, trim($commandTester->getDisplay(true)));
    }

    public function testOutputNoIssues()
    {
        $dashboardMock = $this->getMock('Gh\Dashboard\Dashboard', array('getConfig', 'getIssues'));
        $dashboardMock->expects($this->once())->method('getConfig')->willReturn($this->getConfig());
        $dashboardMock->expects($this->once())->method('getIssues')->willReturn(array());

        $application = new Application();
        $dashboardMock->setApplication($application);

        $commandTester = new CommandTester($dashboardMock);
        $commandTester->execute(array());

        $this->assertEquals(OutputFixtures::NO_ISSUES, trim($commandTester->getDisplay(true)));
    }

    public function testInputParams()
    {
        $dashboardMock = $this->getMock('Gh\Dashboard\Dashboard', array('getConfig', 'getIssues'));
        $dashboardMock->expects($this->once())->method('getConfig')->willReturn($this->getConfig());
        $dashboardMock->expects($this->once())->method('getIssues')->with(
            $this->equalTo('12345abcde'),
            $this->equalTo('bar'),
            $this->equalTo('created'),
            $this->equalTo('closed')
        )->willReturn($this->getIssues());

        $application = new Application();
        $dashboardMock->setApplication($application);

        $commandTester = new CommandTester($dashboardMock);
        $commandTester->execute(array('--organization' => 'bar', '--filter' => 'created', '--state' => 'closed'));
    }

    private function getConfig() {
        return [
            'access_token' => '12345abcde',
            'default_organization' => 'foo',
            'default_filter' => 'mentioned',
            'default_state' => 'open',
        ];
    }

    private function getIssues() {
        $json = file_get_contents(__DIR__ . '/../Fixtures/DefaultIssue.json');

        return json_decode($json);
    }
}
