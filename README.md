# Adrenth.Redirect

## First anniversary!

**You can get 50% discount ($7,49) if you use the Coupon code `1YEAR`. This coupon code is valid until 25th of April 2017.**

## The #1 Redirect plugin for October CMS ([Editors' choice](http://octobercms.com/plugins/featured))

This is the best Redirect-plugin for October CMS. With this plugin installed you can manage redirects directly from October CMS' beautiful interface. Many webmasters and SEO specialists use redirects to optimise their website for search engines. This plugin allows you to manage such redirects with a nice and user-friendly interface.

## What does this plugin offer?

This plugin adds a 'Redirects' section to the main menu of October CMS. This plugin has a unique and fast matching algorithm to match your redirects before your website is being rendered.

## Features

* **Quick** matching algorithm
* A **test** utility for redirects
* Matching using **placeholders** (dynamic paths)
* Match placeholders using **regular expressions**
* **Exact** path matching
* **Importing** and **exporting** redirect rules
* **Schedule** redirects (e.g. active for 2 months)
* Redirect to **external** URLs
* Redirect to **internal** CMS pages
* Redirect **log**
* **Categorize** redirects
* **Statistics**
    * Hits per redirect
    * Popular redirects per month (top 10)
    * Popular crawlers per month (top 10)
    * Number of redirects per month
    * And more...
* Multilingual ***(Need help translating! Contact me at adrenth@gmail.com)***
* Supports MySQL, SQLite and PostgreSQL
* HTTP status codes 301, 302, 303, 404, 410
* Caching

## Currently in development

**Redirect TestLab**

![Imgur](http://i.imgur.com/5ZxHKWV.png)

With the Redirect TestLab you will be able to mass test (a selection) of redirect with several testers. Currently the following testers are finished:

* A redirect loop tester (if any of your redirects will result in a loop)
* A redirect count tester (counts the times your a request will be redirected until it reaches its destination)
* A response code tester (tests if the returned response code is ok)
* A redirect match tester (tests if your redirect will match the redirect you have configured).
* Final destination URL tester (determines the final destination URL).

You will be able to check all your redirects at once and check if there is any error in your configuration.

** Redirect Statistics **

The Statistics dashboard will be improved over time to get more insight in all your redirect traffic.

![Imgur](https://i.imgur.com/nb5m7bs.png)

## Upcoming features

* Improved performance
* New UI

## Redirection

It is important for SEO create redirects of non-existent pages on your website. This plugin allows you to manage such redirects with a nice and user friendly user interface.

## Redirect types

This plugins ships with two types of redirects:

* **Exact**; performs an exact match on the Source path.
* **Placeholders**; matches placeholders like {id} or {category} (like the defined routes in Symfony or Laravel framework).

## Redirect target types

This plugin allows you to redirect to the following types:

* An internal path
* An internal CMS Page
* An external URL

## Placeholders

A placeholder is a dynamic piece in a URL surrounded with curly braces. 
For example:

````
/my-blog/{category}/{id}
````

A placeholder can be replaced by a matched value:

````
/my-blog/hobbies/123
````

Any placeholder can be attached to a **requirement**. A **requirement** consists of a `placeholder`, `requirement` and an optional `replacement` value.

Example:

````
Request path:
/blog.php?category=mouse&id=1337

Redirect Rule: Source path
/blog.php?category={category}&id={id}

Redirect Rule: Target path
/blog/{category}/{id}

Result:
/blog/mouse/1337
````

* The requirement for `{category}` would be: `[a-zA-Z]` or could be more specific like `(dog|cat|mouse)`.
* The requirement for `{id}` would be: `[0-9]+`.

**Replacement value**

A requirement can also contain a replacement value. Provide this replacement value if you need to rewrite a certain placeholder to a static value.

Example:

The requirement for `{category}` is `(dog|cat|mouse)`, with replacement value `animals`.

````
Request path:
/blog.php?category=mouse&id=1337

Redirect Rule: Source path 
/blog.php?category={category}&id={id}

Redirect Rule: Target path
/blog/{category}/{id}

Result:
/blog/animals/1337
````

![](https://i.imgur.com/928z7pI.png)

Result in TestLab:

![](https://i.imgur.com/BswnUAo.png)

## Redirect Target

As of version 1.1.0 you can select a CMS Page as a Redirect target. Placeholders are supported. Let's asume there is a page 'Blog' with the following URL: `/blog/:category/:subcategory`. 

It is possible to create a Redirect with placeholders that has this CMS Page as a target:

````
Redirect with:
Source: `/blog.php?cat={category}&subcat={subcategory}`
Placeholders: {category}, {subcategory}
Target: CMS Page `Blog`

Request path: /blog.php?cat=news&subcat=general
Result: /blog/news/general
````

## Supported database platforms

* MySQL
* PostgreSQL
* SQLite

## Caching

If your website has a lot of redirects it is recommended to enable redirect caching. You can enable redirect caching in the settings panel of this plugin.
 
Only cache drivers which support tagged cache are supported. So driver `file` and `database` are not supported. Hence the fact that database and file caching do not increase performance. So it is recommended to use an in-memory caching solution like `memcached` or `redis`.

### How caching works

If caching is enabled (and supported) every request which is handled by this plugin will be cached. It will be stored with tag `Adrenth.Redirect`.

When you modify a redirect all redirect cache will be invalidated automatically. It is also possible to manually clear the cache using the 'Clear cache' button in the Backend.

## Placeholders

This plugin makes advantage of the `symfony/routing` package. So if you need more info on how to make placeholder requirements for your redirection URLs, please go to: https://symfony.com/doc/current/components/routing/introduction.html#usage

## Contribution

If you like this plugin translated to your language, please contribute. The repository for the language files can be found here: https://github.com/adrenth/redirect-lang

## Questions? Need help?

If you have any question about how to use this plugin, please don't hesitate to contact me. I'm happy to help you. You can also visit the support forum and drop your questions/issues there.

Kind regards,

Alwin Drenth -- *Author of the Redirect plugin*

## Other plugins by [Alwin Drenth](http://octobercms.com/author/Adrenth)

![HtmlPurifier](http://octobercms.com/storage/app/uploads/public/588/334/987/thumb_6466_64x64_0_0_auto.png)

[**HtmlPurifier**](http://octobercms.com/plugin/adrenth-htmlpurifier) -  *Adds a standards compliant HTML filter to October CMS.*

![RssFetcher](http://octobercms.com/storage/app/uploads/public/567/69d/038/thumb_3541_64x64_0_0_auto.png)

[**RssFetcher**](http://octobercms.com/plugin/adrenth-rssfetcher) - *Fetches RSS/Atom feeds from different sources to publish on your website or dashboard.*
