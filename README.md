# Add Url Rewrites to your Laravel Nova powered application

Easy to use Url rewrite package. Allowing you to have pretty urls for your Laravel Nova powered application. 

## Requirements

This package requires Laravel 5.8 or higher, PHP 7.2 or higher.

## Installation

You can install the package via composer:

``` bash
composer require rjvandoesburg/laravel-nova-url-rewrite
```

The package will automatically register itself.

### RewriteController

Add the following to your routes files to add the RewriteController to your application. 
```php
Route::NovaUrlRewrite()
```
This will register the following route: `{request_path}` => `Rjvandoesburg\NovaUrlRewrite\Http\Controllers\UrlRewriteController` with your application.
It is recommended to put this as low as possible because it is a `catch_all` route!

### Publish

The package comes with some configuration, translations and with a migration available for publishing.

```bash
php artisan vendor:publish --provider="Rjvandoesburg\NovaUrlRewrite\NovaUrlRewriteServiceProvider" --tag="nova-url-rewrite-config"
php artisan vendor:publish --provider="Rjvandoesburg\NovaUrlRewrite\NovaUrlRewriteServiceProvider" --tag="nova-url-rewrite-migrations"
php artisan vendor:publish --provider="Rjvandoesburg\NovaUrlRewrite\NovaUrlRewriteServiceProvider" --tag="nova-url-rewrite-translations"
```

If you've published the migrations don't forget to run `php artisan migrate`!
Running the migrations will add a table `url_rewrites`, however the name of this table is configurable in the `url_rewrite.php` config file.

The contents of the config file are as followed:

```php
<?php

return [
    'models' => [
        'url_rewrite' => \Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite::class,
    ],

    'tables' => [
        'url_rewrites' => 'url_rewrites'
    ]
];

```

At this point only two items are configurable, the table name and the model to use. 
Should you choose to extend/alter the given model you are free to do so.

### Nova resource

To view the url_rewrites in the admin, you need to register the tool in your `NovaServiceProvider`
```php
/**
 * Get the tools that should be listed in the Nova sidebar.
 *
 * @return array
 */
public function tools()
{
    return [
        \Rjvandoesburg\NovaUrlRewrite\NovaUrlRewriteTool::make(),
    ];
}
``` 
It is possible to alter the resource used by using the method `setUrlRewriteResource` like so:
```php
    return [
        (\Rjvandoesburg\NovaUrlRewrite\NovaUrlRewriteTool::make())
            ->setUrlRewriteResource(\Rjvandoesburg\NovaUrlRewrite\Nova\UrlRewrite::class),
    ];
```

Of course you need to set it to your own resource.

## Usage

The default Url Rewrite has the following fields:
* `id` - The id of the url rewrite (can be used for regenerating the rule)
* `group` - The group, used in combination with `request_path` defaults to `0` but this allows you to have duplicate a `request_path` for say multiple stores
* `request_path` - The path the user sees/navigates to in the browser
* `target_path` - The location the user should see (or be redirected to)
* `redirect_type` - Integer specifying what type of url rewrite it is (`0 = Forward`, `1 = Permanent`, `2 = Found`)
* `description` - A field where you can specify any details about the rewrite
* `model_type` - Used in combination with `model_id` this is a Morph field which can be used to generate the `target_path`
* `model_id` - Used in combination with `model_type` this is a Morph field which can be used to generate the `target_path`
* `resource_type` - This value allows you to 'attach' a Nova Resource to a redirect url. When this is set in combination with the model, the `UriKey` of the resource will be used in generating the `target_path`
* `created_at` - When the url rewrite was created
* `updated_at` - When the url rewrite was last updates

### Forward request

Let's say you've registered the following route `/product/{id}` and you have a product 'Apple Airpods' with an id of 5.
When you visit `/apple-airpods` this package will forward the request to the provided target path but keeps the url clean.

### Redirect request

Let's say you used to have a url `mac-book-air` but the product name has changed so you've added a new url `mac-book-rock`.
To prevent people hitting a 404 page you can add a redirect url rewrite.

Both `301 Moved Permanently` and `302 Found` (previously described as `Moved Temporarily`) are available.

When using a redirect request the url will change!

Both an internal url or external url is possible for target path when using the redirect type.

### Creating url rewrites via Nova

When using nova for url rewrites you have to provide both the `request_path` and `target_path`, the `target_path` cannot be generated using the `Create Url Rewrite` page in Nova. (yet)

### Creating url rewrites using the builder

This package comes with a 'builder' that allows you to create the Url rewrites on the fly, usually after a model created so it can get the `target_path` based on the model information.

First you need to DI the repository, either use the constructor or pluck it from the container.

```php
$repository = app(\Rjvandoesburg\NovaUrlRewrite\Contracts\UrlRewriteRepository::class);

$builder = $repository->getRewriteBuilder();
```

Now that we have an instance of the builder we can construct a url rewrite.
Do note that the iterface only requires 2 methods `create` and `regenerate(UrlRewrite $urlRewrite)`
This is because the repository delegates to the builder when creating or regenerating url rewrites.

The default `UrlRewriteBuilder` has the following methods available:

* `group(int $group): self` - Set the group
* `requestPath(string $requestPath): self` - Set the request path
* `getRequestPath(): ?string` - Get the request path
* `targetPath(string $targetPath): self` - Set the target path (not required when using a model or model + resource)
* `getTargetPath(): ?string` - Get the target path set by `targetPath` or generate one based on the model or model + resource
* `redirectType(int $type): self` - Set the redirect type
* `description(string $description): self` - Set the description
* `model(Model $model): self` - Set the model
* `resource(\Laravel\Nova\Resource $resource): self` - Set the resource (if the resource contains a linked model (resource) the `model` method is called with the resource)
* `unique(bool $unique = true): self` - Set if the generator should keep looking until it hits a unique url (e.g. `apple-airpods-1`, `apple-airpods-2` etc.)
* `create(): UrlRewrite` - Create the url rewrite, attach the model (if set) and return it
* `regenerate(UrlRewrite $urlRewrite): bool` - Regenerate the url rewrite (useful if the `UriKey` of the resource has changed)

Now for some examples.

Adding a simple url rewrite:
```php
$builder->requestPath('/apple-airpods')
        ->targetPath('/products/5');

$urlRewrite = $repository->create($builder);
```

Adding a url rewrite based on a model:

```php
$product = \App\Models\Product::find(5);

$builder->requestPath('/apple-airpods')
        ->unique()
        ->model($product);

// Because no target_path was provided the builder will look at the provided model/resource and generates a url based on their names.
$urlRewrite = $repository->create($builder);
```

And a very extensive one:
```php
$product = \App\Models\Product::find(5);
$resource = new \App\Nova\Product($product);

$builder->requestPath('/apple-airpods')
        ->group(0) // Defaults to 0
        ->resource($resource)
        ->unique(UrlRewrite)
        ->redirectType(\Rjvandoesburg\NovaUrlRewrite\Models\UrlRewrite::FORWARD)
        ->model($product) // This is redundant as the model is bound to the resource
        ->description('Landing page for the NEW apple airpods');

$urlRewrite = $repository->create($builder);
```

**Now do note that `target_path` is not validated if the route 'exists' in your application**
If this is something you need that you could extend the current builder or add an observer to the UrlRewrite model.

### Trait

This package comes with a trait `\Rjvandoesburg\NovaUrlRewrite\Models\Traits\UrlRewriteable`.
You can add the trait to a model that is used with Url Rewrites and it will add a relationship `urlRewrites` to the model returning a collection of url rewrites.

### Regeneration

If you need or want to regenerate one or multiple url rewrites you can do this via Nova actions or via CLI.
To rewrite a url the minimum requirement is that the url rewrite has a model, otherwise we would not know what to rewrite to :)

To regenerate the urls via the CLI use the following command.
```bash
php artisan nova-url-rewrite:regenerate {id?} --group=[default: 0]
```

If no `id` is specified, all urls will be tried.
By default only urls in group `0` are handled. If you wish to regenerate for another group you need to specify that using the group option.

## TODO

* Add resource search to UrlRewrite resource (within Nova) e.g. a user can create a new url rewrite from Nova to a resource
* Caching!

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Robert-John van Doesburg](https://github.com/rjvandoesburg)
- [All Contributors](../../contributors)

Special thanks for Spatie for their guidelines and their packages as an inspiration
- [Spatie](https://spatie.be)

I would also like to thank Ruthger Idema for his implementation of Url rewrites
- [Ruthger Idema](https://github.com/ruthgeridema)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
