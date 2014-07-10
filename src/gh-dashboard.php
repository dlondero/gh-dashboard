#!/usr/bin/php
<?php

    $config = dirname(__FILE__) . '/../config/config.ini';

    if (!is_file($config)) {
        echo "\e[00;31mNo config.ini file found!\e[0m\n";
        exit(1);
    }

    $iniConfig = parse_ini_file($config);

    if (!isset($iniConfig['default_organization'])
        || !isset($iniConfig['default_filter'])
        || !isset($iniConfig['default_state'])
        || !isset($iniConfig['access_token'])) {
        echo "\e[00;31mPlease define your default values and/or access_token in config.ini file.\e[0m\n";
        exit(1);
    }

    $organization = $iniConfig['default_organization'];
    $filter = $iniConfig['default_filter'];
    $state = $iniConfig['default_state'];
    $accessToken = $iniConfig['access_token'];

    if ($paramList = array_slice($argv, 1)) {
        foreach ($paramList as $param) {
            $param = explode('=', $param);
            $paramType = $param[0];
            $paramValue = $param[1];

            switch ($paramType) {
                case '--org':
                    $organization = isset($paramValue) ? $paramValue : $organization;
                    break;
                case '--filter':
                    $filter = (isset($paramValue) && in_array($paramValue, ['assigned', 'created', 'mentioned', 'subscribed', 'all'])) ? $paramValue : $filter;
                    break;
                case '--state':
                    $state = (isset($paramValue) && in_array($paramValue, ['open', 'closed', 'all'])) ? $paramValue : $state;
                    break;
            }
        }
    }

    $url = "https://api.github.com/orgs/{$organization}/issues?filter={$filter}&state={$state}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "{$accessToken}:x-oauth-basic");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Missing feature to list issues mentioning me in organization dashboard!');
    $output = curl_exec($ch);
    curl_close($ch);

    $issueList = json_decode($output);
    $groupedIssueList = [];

    foreach($issueList as $issue) {
        $groupedIssueList[$issue->repository->full_name][] = $issue;
    }

    foreach ($groupedIssueList as $repository => $issueList) {
        echo "\e[01;35m[" . $repository . "]\e[0m\n";

        foreach($issueList as $issue) {
            echo str_repeat(' ', 4) . "\e[00;32m" . $issue->title . "\e[0m -> " . $issue->html_url . "\n";
        }
    }

?>
