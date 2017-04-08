# Adrenth.Redirect documentation

This plugin should be easy to understand if you are familiar with the basics of the web. If you have issues setting up some redirects, please do not hesitate to contact me.

## Redirect types

This plugins ships with two types of redirects:

* **Exact**; performs an exact match on the Source path.
* **Placeholders**; matches placeholders like {id} or {category} (like the defined routes in Symfony or Laravel framework).

## Redirect target types

This plugin allows you to redirect to the following types:

* An internal path
* An internal CMS Page
* An internal Static Page (`RainLab.Pages` plugin)
* An external URL

## Scheme matching

This plugin allows you to match requests from a `http://` scheme to a `https://` scheme and vice versa.

## Placeholders

Every placeholder can be attached to a requirement. A requirement consists of a `placeholder`, `requirement` and an optional `replacement` value.

Example:

````
Input path:
/blog.php?category=cat&id=145

Source path: 
/blog.php?category={category}&id={id}

Target path:
/blog/{category}/{id}

Result path:
/blog/cat/145
````

* The requirement for `{category}` would be: `[a-zA-Z]` or could be more specific like `(dog|cat|mouse)`.
* The requirement for `{id}` would be: `[0-9]+`.

**Replacement value**

A requirement can also contain a replacement value. Provide this replacement value if you need to rewrite a certain placeholder to a static value.

Example:

The requirement for `{category}` is `(dog|cat|mouse)`, with replacement value `animals`.

````
Input path:
/blog.php?category=mouse&id=1337

Source path: 
/blog.php?category={category}&id={id}

Target path:
/blog/{category}/{id}

Result:
/blog/animals/1337
````

![](https://i.imgur.com/928z7pI.png)

Result in TestLab:

![](https://i.imgur.com/BswnUAo.png)


## Redirect Target

You can select a CMS Page as a Redirect target. Placeholders are supported. Let's assume there is a page 'Blog' with the following URL: `/blog/:category/:subcategory`. 

It is possible to create a Redirect with placeholders that has this CMS Page as a target:

````
Redirect with:
Source: `/blog.php?cat={category}&subcat={subcategory}`
Placeholders: {category}, {subcategory}
Target: CMS Page `Blog`

Request path: /blog.php?cat=news&subcat=general
Result: /blog/news/general
````
