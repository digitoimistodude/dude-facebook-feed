# Dude Facebook feed
WordPress plugin to get latest latest posts from Facebook profile or page.

Basically this plugin fetches posts from profile or page, saves those to transient and next requests will be served from there. After transient has expired and deleted, new fetch from Facebook will be made and saved to transient. This implements very simple cache.

Handcrafted with love at [Digitoimisto Dude Oy](http://dude.fi), a Finnish boutique digital agency in the center of Jyväskylä.

## Table of contents
1. [Please note before using](#please-note-before-using)
2. [License](#license)
3. [Usage](#usage)
  1. [Hooks](#hooks)
4. [Composer](#composer)
5. [Contributing](#contributing)

### Please note before using
This plugin is not meant to be "plugin for everyone", it needs at least some basic knowledge about php and css to add it to your site and making it look beautiful.

This is a plugin in development for now, so it may update very often.

### License
Dude Facebook feed is released under the GNU GPL 2 or later.

### Usage
This plugin does not have settings page or provide anything visible on front-end. So it's basically dumb plugin if you don't use any filters listed below.

Only mandatory filter to use is `dude-facebook-feed/access_token`.

Get posts by calling function `dude_facebook_feed()->get_posts()`, pass Facebook id as a only argument. It id can be obtained with [this tool](http://findmyfbid.com/).

#### Hooks
All the settings are set with filters, and there is also few filters to change basic functionality and manipulate data before caching.

##### `dude-facebook-feed/access_token`
You need most likely global access token to get posts from page, it's App ID and secret separated with |. If you are fetching posts from profile, please provide user access token generated with [Graph API Explorer](https://developers.facebook.com/tools/explorer/).

Defaults to empty string.

##### `dude-facebook-feed/posts_transient`
Change name of the transient for posts. Passed arguments are default name and facebook id.

Defaults to `dude-facebook-user-$fbid`.

##### `dude-facebook-feed/api_call_parameters`
Modify api call parameters just before sending those. Only passed argument is array of default parameters.

Defaults to access_token, locale, since, limit and fields wth values described later on this document.

##### `dude-facebook-feed/posts`
Manipulate or use data before it's cached to transient. Only passed argument is array of posts.

##### `dude-facebook-feed/posts_transient_lifetime`
Change posts cache lifetime. Only passed argument is default lifetime in seconds.

Defaults to 600 (= ten minutes).

#### Default call parameters

##### Access token
Default parameters are below, those can be changed with filters named `dude-facebook-feed/parameters/<PARAMETER>`.

``
array(
  ["access_token"]  => ""
  ["locale"]        => "fi_FI"
  ["since"]         => "2015-12-09"
  ["limit"]         => "10"
  ["fields"]        => array(
    "id",
    "created_time",
    "type,message",
    "story",
    "full_picture",
    "link"
  )
)
``

### Composer

To use with composer, run `composer require digitoimistodude/dude-facebook-feed dev-master` in your project directory or add `"digitoimistodude/dude-facebook-feed":"dev-master"` to your composer.json require.

### Contributing
If you have ideas about the theme or spot an issue, please let us know. Before contributing ideas or reporting an issue about "missing" features or things regarding to the nature of that matter, please read [Please note section](#please-note-before-using). Thank you very much.
