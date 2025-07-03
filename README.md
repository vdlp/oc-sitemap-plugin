<p align="center">
	<img height="60px" width="60px" src="https://plugins.vdlp.nl/octobercms/icons/Vdlp.Sitemap.svg">
	<h1 align="center">Vdlp.Sitemap</h1>
</p>

<p align="center">
	<em>This plugin allows developers to create a sitemap.xml using a sitemap definition generator.</em>
</p>

<p align="center">
	<img src="https://badgen.net/packagist/php/vdlp/oc-sitemap-plugin">
	<img src="https://badgen.net/packagist/license/vdlp/oc-sitemap-plugin">
	<img src="https://badgen.net/packagist/v/vdlp/oc-sitemap-plugin/latest">
	<img src="https://badgen.net/badge/cms/October%20CMS">
	<img src="https://badgen.net/badge/type/plugin">
	<img src="https://plugins.vdlp.nl/octobercms/badge/installations.php?plugin=vdlp-sitemap">
</p>

## Requirements

- PHP 8.0.2 or higher
- Supports October CMS `3.x` or `4.x`

## Usage

### Sitemap definitions generator

To generate sitemap items you can create your own sitemap definition generator.

Example:

```php
final class DefinitionGenerator implements Contracts\DefinitionGenerator
{
    public function getDefinitions(): Definitions
    {
        $definitions = new Definitions();

        for ($i = 0; $i < 100; $i++) {
            $definitions->addItem(
                (new Definition)->setModifiedAt(Carbon::now())
                    ->setPriority(1)
                    ->setUrl('example.com/page/' . $i)
                    ->setChangeFrequency(Definition::CHANGE_FREQUENCY_ALWAYS)
            );
        }

        return $definitions;
    }
}
```

Register your generator in the `boot` method of your plugin class:

```php
Event::listen(Contracts\SitemapGenerator::GENERATE_EVENT, static function(): DefinitionGenerator {
    return new DefinitionGenerator();
});
```

You can also register multiple generators:

```php
Event::listen(Contracts\SitemapGenerator::GENERATE_EVENT, static function(): array {
    return [
        new DefinitionGeneratorOne(),
        new DefinitionGeneratorTwo(),
        // ..
    ];
});
```

### Invalidate sitemap cache

You can fire an event to invalidate the sitemap cache

```php
Event::fire(Contracts\SitemapGenerator::INVALIDATE_CACHE_EVENT);
```

Or resolve the generator instance and use the invalidate cache method

```php
/** @var SitemapGenerator $sitemapGenerator */
$sitemapGenerator = resolve(Contracts\SitemapGenerator::class);
$sitemapGenerator->invalidateCache();
```

## Update / Add / Delete definitions in cache

First resolve the sitemap generator

```php
/** @var SitemapGenerator $sitemapGenerator */
$sitemapGenerator = resolve(Contracts\SitemapGenerator::class);
```

### Add definitions

```php
$sitemapGenerator->addDefinition(
    (new Definition())
        ->setUrl('example.com/new-url')
        ->setModifiedAt(Carbon::now())
        ->setChangeFrequency(Definition::CHANGE_FREQUENCY_YEARLY)
        ->setPriority(5)
);
```

### Update definitions

> Note, definitions are updated by their URL.

```php
$sitemapGenerator->updateDefinition(
    (new Definition())
        ->setUrl('example.com/page/1')
        ->setModifiedAt(Carbon::parse('1900-10-10'))
        ->setPriority(7)
        ->setChangeFrequency(Definition::CHANGE_FREQUENCY_HOURLY),
    'example.com/page/0' // (optional) specify the url to update in cache, when old url is null the definition url will be used.
);
```

### Update or add definitions

```php
$sitemapGenerator->updateOrAddDefinition(
    (new Definition())
        ->setUrl('example.com/create-or-add')
        ->setModifiedAt(Carbon::now())
        ->setChangeFrequency(Definition::CHANGE_FREQUENCY_YEARLY)
        ->setPriority(5),
    null // (optional) specify the url to update in cache, when old url is null the definition url will be used.
);
```

### Delete definitions

```php
$sitemapGenerator->deleteDefinition('example.com/new-url');
```

## Exclude URLs from sitemap

```php
Event::listen(SitemapGenerator::EXCLUDE_URLS_EVENT, static function (): array {
    return [
        'example.com/page/1',
    ];
});
```

## Configuration

Add the plugin configuration to your config folder:

```
php artisan vendor:publish --provider="Vdlp\Sitemap\ServiceProvider" --tag="config"
```

You can change the amount of seconds the sitemap is cached in your `.env` file.
You can also cache the sitemap forever.

 ```dotenv
VDLP_SITEMAP_CACHE_TIME=3600
VDLP_SITEMAP_CACHE_FOREVER=false
```

### ConfigResolver

Optionally you can override how the sitemap config should be resolved by giving your own ConfigResolver implementation in the config file.
This can be useful for multisite projects, where the sitemap should be cached per domain.

```php
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Vdlp\Sitemap\Classes\Contracts\ConfigResolver;
use Vdlp\Sitemap\Classes\Dto\SitemapConfig;

final class MultisiteConfigResolver implements ConfigResolver
{
    public function __construct(private Repository $config, private Request $request)
    {
    }

    public function getConfig(): SitemapConfig
    {
        $domain = $this->request->getHost();

        return new SitemapConfig(
            'vdlp_sitemap_cache_' . $domain,
            'vdlp_sitemap_definitions_' . $domain,
            sprintf('vdlp/sitemap/sitemap_%s.xml', $domain),
            (int) $this->config->get('sitemap.cache_time', 3600),
            (bool) $this->config->get('sitemap.cache_forever', false)
        );
    }
}
```

## Issues

If you have issues using this plugin. Please create an issue on GitHub or contact us at [octobercms@vdlp.nl]().

## Contribution

Any help is appreciated. Or feel free to create a Pull Request on GitHub.
