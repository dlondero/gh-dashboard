<?php

namespace Gh\Dashboard;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Dashboard extends Command
{
    protected function configure()
    {
        $this
            ->setName('gh-dashboard')
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
        $configPath = $this->getConfigPath();
        $config = $this->getConfig($configPath, $input, $output);

        $organization = $config['default_organization'];
        $filter = $config['default_filter'];
        $state = $config['default_state'];
        $accessToken = $config['access_token'];

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
     * @param string $accessToken
     * @param string $organization
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
     * @param string $configPath
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function getConfig($configPath, $input, $output)
    {
        $helper = $this->getHelper('question');
        $writeConfig = false;

        if (!is_dir($this->getConfigDirPath())) {
            mkdir($this->getConfigDirPath(), 0755);
            file_put_contents($configPath, null);
        }

        $config = json_decode(file_get_contents($configPath), true);

        if (!isset($config['access_token'])) {
            $question = new Question('<question>Please enter your access_token: </question>');
            $accessToken = $helper->ask($input, $output, $question);

            $config['access_token'] = $accessToken;
            $writeConfig = true;
        }

        if (!isset($config['default_organization'])) {
            $question = new Question('<question>Please enter your default organization: </question>');
            $defaultOrganization = $helper->ask($input, $output, $question);

            $config['default_organization'] = $defaultOrganization;
            $writeConfig = true;
        }

        if (!isset($config['default_filter'])) {
            $question = new ChoiceQuestion(
                '<question>Please select your default filter (defaults to mentioned)</question>',
                array('assigned', 'created', 'mentioned', 'subscribed', 'all'),
                '2'
            );
            $defaultFilter = $helper->ask($input, $output, $question);

            $config['default_filter'] = $defaultFilter;
            $writeConfig = true;
        }

        if (!isset($config['default_state'])) {
            $question = new ChoiceQuestion(
                '<question>Please select your default state (defaults to open)</question>',
                array('open', 'closed', 'all'),
                '0'
            );
            $defaultState = $helper->ask($input, $output, $question);

            $config['default_state'] = $defaultState;
            $writeConfig = true;
        }

        if ($writeConfig && false !== file_put_contents($configPath, json_encode($config))) {
            $output->writeln("<info>Configuration written correctly to $configPath.</info>");
        }

        return $config;
    }

    /**
     * @return string
     */
    protected function getConfigDirPath()
    {
        return $_SERVER['HOME'] . DIRECTORY_SEPARATOR . '.gh-dashboard';
    }

    /**
     * @return string
     */
    protected function getConfigPath()
    {
        return $this->getConfigDirPath() . DIRECTORY_SEPARATOR . 'config.json';
    }
}
