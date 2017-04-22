# Adrenth.Redirect

## The #1 Redirect plugin for October CMS ([Editors' choice](http://octobercms.com/plugins/featured))

This is the best Redirect-plugin for October CMS. With this plugin installed you can manage redirects directly from October CMS' beautiful interface. Many webmasters and SEO specialists use redirects to optimise their website for search engines. This plugin allows you to manage such redirects with a nice and user-friendly interface.

## What does this plugin offer?

This plugin adds a 'Redirects' section to the main menu of October CMS. This plugin has a unique and fast matching algorithm to match your redirects before your website is being rendered.

!![560x315](//www.youtube.com/embed/R1s7yEEKrgA)

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

## Supported database platforms

* MySQL
* PostgreSQL
* SQLite

## Supported HTTP status codes

* HTTP/1.1 301 Moved Permanently
* HTTP/1.1 302 Found
* HTTP/1.1 303 See Other
* HTTP/1.1 404 Not Found
* HTTP/1.1 410 Gone

## Supported HTTP request methods

* `GET`
* `POST`
* `HEAD`

## Performance

All redirects are stored in the database and will be automatically "published" to a file which the internal redirect mechanism uses to determine if a certain request needs to be redirected. This is way faster than querying a database.

This plugin is designed to be fast and should have no negative effect on the performance of your website.

To gain maximum performance with this plugin:

* Use PHP7 (really you should), this increases the performance with 200%
* Enable redirect caching using a "in-memory" caching method (see Caching).
* Maintain your redirects frequently to keep the number of redirects as low as possible.
* Try to use placeholders to keep your number of redirect low (less redirects is better performance).

## Caching

If your website has a lot of redirects it is recommended to enable redirect caching. You can enable redirect caching in the settings panel of this plugin.
 
Only cache drivers which support tagged cache are supported. So driver `file` and `database` are not supported. For this plugin database and file caching do not increase performance, but can actually have a negative influence on performance. So it is recommended to use an in-memory caching solution like `memcached` or `redis`.

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

---

If you 💙 this quality plugin as much as I do, please **rate my plugin**, [contribute](https://github.com/adrenth/redirect-lang) or consider a [PayPal donation](https://www.paypal.me/adrenth) to support this plugin and my other quality October CMS plugins.

---

## Other plugins by [Alwin Drenth](http://octobercms.com/author/Adrenth)

![HtmlPurifier](http://octobercms.com/storage/app/uploads/public/588/334/987/thumb_6466_64x64_0_0_auto.png)

[**HtmlPurifier**](http://octobercms.com/plugin/adrenth-htmlpurifier) -  *Adds a standards compliant HTML filter to October CMS.*

![RssFetcher](http://octobercms.com/storage/app/uploads/public/567/69d/038/thumb_3541_64x64_0_0_auto.png)

[**RssFetcher**](http://octobercms.com/plugin/adrenth-rssfetcher) - *Fetches RSS/Atom feeds from different sources to publish on your website or dashboard.*
