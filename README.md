yourls-api-save-by-keyword [![Listed in Awesome YOURLS!](https://img.shields.io/badge/Awesome-YOURLS-C5A3BE)](https://github.com/YOURLS/awesome-yourls/)
===================

Adds a new action `save_by_keyword` to the [YOURLS API](https://yourls.org/#API).

This action either updates the long URL of an existing keyword, or creates a new short URL with the supplied keyword. 

This plugin is useful when you prefer to determine your own keywords (e.g: `user-id-1`, `user-id-2`, `user-joe` etc) rather than letting YOURLS generating random ones (e.g: `a93al`, `n9sj8`, etc) and you need to keep their long URLs always up to date.

With this action, you may save up to three steps: check if an existing keyword already exists, then delete it, and then generating again under the same keyword.

The request parameters are:
```
username <api_username>
password <api_password>
action: "save_by_keyword"
format: "json"
url: <required: the long URL to shorten>
keyword: <required: the keyword>
title: <optional: the shorten URL title>
```

**TIP:** In case you need to allow multiple short URLs for a same long URL, you need to set the parameter `YOURLS_UNIQUE_URLS` to `false`.
```php
# config.php
define( 'YOURLS_UNIQUE_URLS', false );
```

# How to
* In `/user/plugins`, create a new folder named `yourls-api-save-by-keyword`
* Drop these files in that directory
* Go to the Plugins administration page and activate the plugin
* Have fun
