gh-dashboard
============

Command line dashboard for GitHub

GitHub has a very nice organization dashboard where you can see all issues of the repositories of the org and use some filters to control issues "Assigned to you" and "Created by you". Unfortunately there's no "Mentioning you" like when browsing issues in a single repository. And this sucks, because I use a lot the org dashboard to have an overview of all the issues I need to look at.

I pinged @github on Twitter a couple of times reporting this missing feature with no results, then wrote them using their contact form and quickly got a reply telling me that they "added a +1 to this on our internal Feature Request List" but also "We can't promise if we may add this, however your feedback is definitely appreciated". Thanks for you honesty.

I then looked at the [API](https://developer.github.com/v3/issues/#list-issues) and found that filtering for `mentioned` issues looks quite easy so I decided to write some code in order to consume the API and get a CLI tool to solve the issue.
