# codeigniter4-reddit
Reddit SDK for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-reddit/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-reddit/actions?query=workflow%3A%22PHPUnit)
[![](https://github.com/tattersoftware/codeigniter4-reddit/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/codeigniter4-reddit/actions?query=workflow%3A%22PHPStan)

## Quick Start

1. Install with Composer: `> composer require tatter/reddit`
2. Supply Reddit credentials in **.env**
3. Get API results:
```
foreach (service('reddit')->fetch('new') as $thing)
{
	echo (string) $thing; // "Comment" or "Link"
	echo $thing->link_permalink; // E.g. "https://www.reddit.com/r/pythonforengineers/comments/jox9zy/great_video_for_the_python_programers"
}

```

## Description

**Reddit SDK** provides a framework-ready wrapper to the API endpoints
describe in the [Reddit API documentation](https://www.reddit.com/dev/api).


## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**examples/Reddit.php** to **app/Config/** and follow the instructions
in the comments. If no config file is found in **app/Config** then the library will use its own.

## Credentials

This library requires a valid Reddit application to acquire access tokens that work with
API. For more details read the [Reddit OAuth2 wiki](https://github.com/reddit-archive/reddit/wiki/OAuth2).

1. Login to Reddit and visit the "authorized applications" page (https://www.reddit.com/prefs/apps/)
2. Under "developed applications" select "create an app..."
3. Select "script" as the application type
4. Provide a name, description, and URLs in the required text fields
5. Select "create app"

Once your application is created you will need to copy the "client ID" and "secret" (see the
wiki article above fo help). Add these along with your username and password into your
project's **.env** file, for example:
```
#--------------------------------------------------------------------
# REDDIT API
#--------------------------------------------------------------------

reddit.clientId = as98-asdn3h93r
reddit.clientSecret = LKhsa-ASJDn9a8sdion_laskdn0
reddit.username = MyFiRsTrEdItTbOt
reddit.password = ReallySecurePassword321
```

## Usage

The easiest way to access the client is via the CodeIgniter's Services:

	$reddit = service('reddit');

The client will handle authentication (assuming your credentials are valid), rate limiting,
response filtering and formatting. Access client methods in chains to set up the request,
then use `fetch()` to kick it off:

	$comments = $reddit->subreddit('catgifs')->limit(10)->fetch('comments');

For more advanced needs you may use the `request($uri, $data, $query)` method which returns
the actual `Response` object, providing access to headers, etc. See also
**HTTP/RedditRequest.php** and **HTTP/RedditResponse.php** for some of the API handling
done "under the hood".

## Troubleshooting

Should something go wrong all anticipated exceptions are wrapped in `Tatter\Reddit\Exceptions\RedditException`,
so you can catch them and figure out what happened:
```
try
{
	$comments = $reddit->subreddit('php')->fetch('new');
}
catch (\Tatter\Reddit\Exceptions\RedditException $e)
{
	echo $e->getMessage(); // "API responded with an error: Invalid authorization"
}
```
