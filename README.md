gh-dashboard
============

Command line dashboard for GitHub

GitHub has a very nice organization dashboard where you can see all issues of the repositories of the org and use some filters to control issues
"Assigned to you" and "Created by you". Unfortunately there's no "Mentioning you" like when browsing issues in a single repository. And this sucks,
because I use a lot the org dashboard to have an overview of all the issues I need to look at.

I pinged @github on Twitter a couple of times reporting this missing feature with no results, then wrote them using their contact form and quickly
got a reply telling me that they "added a +1 to this on our internal Feature Request List" but also "We can't promise if we may add this, however
your feedback is definitely appreciated". Thanks for you honesty.

I then looked at the [API](https://developer.github.com/v3/issues/#list-issues) and found that filtering for `mentioned` issues looks quite easy so
I decided to write some code in order to consume the API and get a CLI tool to solve the issue.

![gh-dashboard output](https://dl.dropboxusercontent.com/u/6656849/gh-dashboard-1.png)

### Install

#### 1) Installing system-wide using composer (recommended)

```bash
$ composer global require dlondero/gh-dashboard=dev-master@dev
```

If it is the first time you globally install a dependency then make sure
you include `~/.composer/vendor/bin` in $PATH as shown [here](http://getcomposer.org/doc/03-cli.md#global).

##### Always keep your gh/dashboard installation up to date:

```bash
$ composer global update dlondero/gh-dashboard
```

#### 2) Installing manually:

 1. Clone this repository
 2. Link `gh-dashboard` in order to use it from wherever you need `ln -s ~/yourpath/gh-dashboard/bin/gh-dashboard /usr/local/bin/gh-dashboard`

### Setup
On the first run you will be asked for an [access token](https://github.com/blog/1509-personal-api-tokens) and some defaults (organization, filter 
and state) which will be used when running gh-dashboard without specifying any option.

### Usage

Use default organization and filter issues showing the ones where you are `mentioned` and in `open` status.
```bash
$ gh-dashboard
```

Or specify the parameters you want
```bash
$ gh-dashboard [--organization="..."] [--filter="..."] [--state="..."]
```

you can see available `filters` and `states` on the [API documentation](https://developer.github.com/v3/issues/#list-issues).
