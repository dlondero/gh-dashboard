<?php

namespace Gh\Dashboard;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Dashboard extends Command
{
    protected function configure()
    {
        $this
            ->setName('dashboard')
            ->setDescription('List issues mentioning me in organization dashboard!')
            ->addOption(
                'organization',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the task will use the organization provided'
            )
            ->addOption(
                'filter',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the task will filter the issues based on value provided'
            )
            ->addOption(
                'state',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the task will show only issues in the state provided'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = dirname(__FILE__) . '/../../config/config.ini';
        $iniConfig = $this->getConfig($config, $output);

        $organization = $iniConfig['default_organization'];
        $filter = $iniConfig['default_filter'];
        $state = $iniConfig['default_state'];
        $accessToken = $iniConfig['access_token'];

        if ($organizationOption = $input->getOption('organization')) {
            $organization = $organizationOption;
        }
        if ($filterOption = $input->getOption('filter')) {
            $filter = $filterOption;
        }
        if ($stateOption = $input->getOption('state')) {
            $state = $stateOption;
        }

        $issueList = $this->getIssues($accessToken, $organization, $filter, $state);

        $groupedIssueList = [];
        foreach ($issueList as $issue) {
            $groupedIssueList[$issue->repository->full_name][] = $issue;
        }

        foreach ($groupedIssueList as $repository => $issueList) {
            $output->writeln("<fg=magenta>[$repository]</fg=magenta>");

            foreach ($issueList as $issue) {
                $output->writeln(str_repeat(' ', 4) . "<fg=green>$issue->title</fg=green><fg=white> -> $issue->html_url</fg=white>");
            }
        }
    }

    /**
     * @param string      $accessToken
     * @param string      $organization
     * @param string|null $filter
     * @param string|null $state
     * @return array
     */
    protected function getIssues($accessToken, $organization, $filter = null, $state = null)
    {
        $url = "https://api.github.com/orgs/{$organization}/issues?filter={$filter}&state={$state}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$accessToken}:x-oauth-basic");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Missing feature to list issues mentioning me in organization dashboard!');
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output);
    }

    /**
     * @param string $config
     * @param OutputInterface $output
     * @return array
     */
    protected function getConfig($config, $output)
    {
        if (!is_file($config)) {
            $output->writeln('<error>No config.ini file found!</error>');
            exit(1);
        }

        $iniConfig = parse_ini_file($config);

        if (!isset($iniConfig['access_token'])) {
            $output->writeln('<error>Please define your access_token in config.ini file.</error>');
            exit(1);
        }

        if (!isset($iniConfig['default_organization'])
            || !isset($iniConfig['default_filter'])
            || !isset($iniConfig['default_state'])
        ) {
            $output->writeln('<error>Please define your default values in config.ini file.</error>');
            exit(1);
        }

        return $iniConfig;
    }
}
