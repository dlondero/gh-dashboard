#!/usr/bin/php
<?php

    $config = dirname(__FILE__) . '/../config/config.ini';

    if (!is_file($config)) {
        echo "\e[00;31mNo config.ini file found!\e[0m\n";
        exit(1);
    }

    $iniConfig = parse_ini_file($config);

    if (!isset($iniConfig['default_organization']) || !isset($iniConfig['access_token'])) {
        echo "\e[00;31mPlease define your default_organization and access_token in config.ini file.\e[0m\n";
        exit(1);
    }

    $defaultOrganization = $iniConfig['default_organization'];
    $accessToken = $iniConfig['access_token'];

    $organization = isset($argv[1]) ? $argv[1] : $defaultOrganization;
    $filter = (isset($argv[2]) && in_array($argv[2], ['assigned', 'created', 'mentioned', 'subscribed', 'all'])) ? $argv[2] : 'mentioned';
    $state = (isset($argv[3]) && in_array($argv[3], ['open', 'closed', 'all'])) ? $argv[3] : 'open';

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
