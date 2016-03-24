# Adrenth.Redirect

An OctoberCMS plugin which allows you to manage redirects for your website.

## Redirection

It is important for SEO create redirects of non-existent pages on your website. This plugin allows you to manage such redirects with a nice and user friendly user interface.

## Redirect types

This plugins ships with two types of redirects:

* **Exact**; performs an exact match on the source URL
* **Placeholders**; matches placeholders like {id} or {category} (like the defined routes in Symfony or Laravel framework)

My plan is to add more redirection types in the future.

## Placeholders

Every placeholder can be attached to a requirement. A requirement consists of a `placeholder`, `requirement` and an optional `replacement` value.

Example:

````
Input URL:

/blog.php?category=cat&id=145

Source URL: 
/blog.php?category={category}&id={id}

Target URL:
/blog/{category}/{id}

Result URL:

/blog/cat/145
````

* The requirement for `{category}` would be: `[a-zA-Z]` or could be more specific like `(dog|cat|mouse)`.
* The requirement for `{id}` would be: `[0-9]+`.

**Replacement value**

A requirement can also contain a replacement value. Provide this replacement value if you need to rewrite a certain placeholder to a static value.

Example:

The requirement for `{category}` is `(dog|cat|mouse)`, with replacement value `animals`.

````
Input URL:
/blog.php?category=mouse&id=1337

Source URL: 
/blog.php?category={category}&id={id}

Target URL:
/blog/{category}/{id}

Result:
/blog/animals/1337
````

## More information

This plugin makes advantage of the `symfony/routing` package. So if you need more info on how to make requirements for your redirection URLs, please go to: [https://symfony.com/doc/current/components/routing/introduction.html#usage]()

## Contribution

If you like to contribute to this plugin feel free to create a Pull Request. But you can also contact me. My contact details can be found in the source code of this project.