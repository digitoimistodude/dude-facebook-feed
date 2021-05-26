| :bangbang: | **This repository is no longer actively maintained. The plugin still works, but does not receive any further updates other than community contributed fixes.**  |
|:------------:|:------------------------------------------------------------------------------------------------------------------------------------------------------------------------|

# Dude Facebook feed
WordPress plugin to get latest latest posts from Facebook profile or page.

Basically this plugin fetches posts from profile or page, saves those to transient and next requests will be served from there. After transient has expired and deleted, new fetch from Facebook will be made and saved to transient. This implements very simple cache.

Handcrafted with love at [Digitoimisto Dude Oy](http://dude.fi), a Finnish boutique digital agency in the center of Jyväskylä.

## Table of contents
1. [Please note before using](#please-note-before-using)
2. [License](#license)
3. [Usage](#usage)
  1. [Usage example for displaying a Facebook page feed](#usage-example-for-displaying-a-facebook-page-feed)
  2. [Limiting feed items](#limiting-feed-items)
  3. [Add support for likes field](#add-support-for-likes-field)
4. [Hooks](#hooks)
5. [Default call parameters](#default-call-parameters)
6. [Composer](#composer)
7. [Contributing](#contributing)

## Please note before using
This plugin is not meant to be "plugin for everyone", it needs at least some basic knowledge about php and css to add it to your site and making it look beautiful.

This is a plugin in development for now, so it may update very often.

## License
Dude Facebook feed is released under the GNU GPL 2 or later.

## Usage
This plugin does not have settings page or provide anything visible on front-end. So it's basically dumb plugin if you don't use any filters listed below.

Only mandatory filter to use is `dude-facebook-feed/parameters/access_token`.

Get posts by calling function `dude_facebook_feed()->get_posts()`, pass Facebook id as a only argument. It id can be obtained with [this tool](http://findmyfbid.com/).

### Usage example for displaying a Facebook page feed

1. Go to [developers.facebook.com](https://developers.facebook.com/) and create app for your WordPress site
2. Generate access token by going [Facebook Graph API Explorer](https://developers.facebook.com/tools/explorer/).

Here comes a tricky part. Facebook let's apps access any pages if the app was made before May 1st, 2018.

**My app was created before May 1st, 2018:**

Select your app in **Application:** dropdown, select **Get Token** and **Get App Token**. Your access_token will look like `appid|appsecret`.

**My app was created after May 1st, 2018:**

You need to be *administrator* in the page that you want to access. You can no longer get access token to any page but if you are the administrator, you don't need to send your app to review. Only if you need to access to any public page will you need to go through the app review process.

Select your app in **Application:** dropdown, select **Get Token** and **Get User Token**. Grant pretty much all privileges.

Select **Get Token** again and select correct page from **Page Access Token**. After that, copy the long "Access token" shown (can be over 200 characters). This is your access_token.

Notice that this access_token will only grant you permission to that page. For every page, you must create separate access_token.

3. Copy **Access Token** and create filter that returns access token to your **functions.php** (remember to change prefix *yourtexdomain* to your app/site name):

```php
<?php
/**
 * Facebook feed
 */
 add_filter('dude-facebook-feed/parameters/access_token', 'yourtexdomain_fb_access_token' );
 function yourtexdomain_fb_access_token() {
    return 'very_long_access_token_from_fb'; // legacy access_token: 'appid|appsecret'
 }
```

4. Get your Facebook page numeric ID from [findmyfbid.com](http://findmyfbid.com/). Go to the page you want your Facebook feed to be displayed, for example **front-page.php** and loop the feed and do stuff (it's always good idea to `var_dump` the data to see what's there to use:

```php
$feed = dude_facebook_feed()->get_posts( '569702083062696' );

foreach ( $feed['data'] as $item ) :

  if ( $item['story'] ) :
    $message = $item['story'];
  else :
    $message = $item['message'];
  endif;

  echo $message;
endforeach;
```

### Limiting feed items

```php
// Limit feed to 6 items
add_filter('dude-facebook-feed/api_call_parameters', 'yourtexdomain_fb_limit' );
function yourtexdomain_fb_limit( $parameters ) {
  $parameters['limit'] = 6;
  return $parameters;
}
```

### Add support for likes field

```php
// Add likes support
add_filter('dude-facebook-feed/parameters/fields', 'yourtexdomain_fb_likes' );
function yourtexdomain_fb_likes( $parameters ) {
  $parameters[] = 'likes';

  return $parameters;
}
```

## Hooks
All the settings are set with filters, and there is also few filters to change basic functionality and manipulate data before caching.

#### `dude-facebook-feed/parameters/access_token`
You need most likely global access token to get posts from page, it's App ID and secret separated with |. If you are fetching posts from profile, please provide user access token generated with [Graph API Explorer](https://developers.facebook.com/tools/explorer/).

Defaults to empty string.

#### `dude-facebook-feed/posts_transient`
Change name of the transient for posts. Passed arguments are default name and facebook id.

Defaults to `dude-facebook-user-$fbid`.

#### `dude-facebook-feed/api_call_parameters`
Modify api call parameters just before sending those. Only passed argument is array of default parameters.

Defaults to access_token, locale, since, limit and fields wth values described later on this document.

#### `dude-facebook-feed/posts`
Manipulate or use data before it's cached to transient. Only passed argument is array of posts.

#### `dude-facebook-feed/posts_transient_lifetime`
Change posts cache lifetime. Only passed argument is default lifetime in seconds.

Defaults to 600 (= ten minutes).

## Default call parameters

Default parameters are below, those can be changed with filters named `dude-facebook-feed/parameters/<PARAMETER>`.

```
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
```

## Composer

To use with composer, run `composer require digitoimistodude/dude-facebook-feed dev-master` in your project directory or add `"digitoimistodude/dude-facebook-feed":"dev-master"` to your composer.json require.

## Contributing
If you have ideas about the theme or spot an issue, please let us know. Before contributing ideas or reporting an issue about "missing" features or things regarding to the nature of that matter, please read [Please note section](#please-note-before-using). Thank you very much.
