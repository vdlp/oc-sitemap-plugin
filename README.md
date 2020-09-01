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

- PHP 7.1 or higher
- This plugin requires the `Vdlp.Sitemap` plugin. 
- October CMS (preferably the latest version).

## Usage

### Sitemap definitions generator

To generate sitemap items you can create your own sitemap definition generator.

Example:

```
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

```
Event::listen(Contracts\SitemapGenerator::GENERATE_EVENT, static function() {
    return new DefinitionGenerator();
});
```

You can also register multiple generators:

```
Event::listen(Contracts\SitemapGenerator::GENERATE_EVENT, static function() {
    return [
        new DefinitionGeneratorOne(), 
        new DefinitionGeneratorTwo(),
        // ..
    ];
});
```

### Invalidate sitemap cache

You can fire an event to invalidate the sitemap cache

```
Event::fire(Contracts\SitemapGenerator::INVALIDATE_CACHE_EVENT);
```

Or resolve the generator instance and use the invalidate cache method

```
/** @var SitemapGenerator $sitemapGenerator */
$sitemapGenerator = resolve(Contracts\SitemapGenerator::class);
$sitemapGenerator->invalidateCache();
```

## Update / Add / Delete definitions in cache

First resolve the sitemap generator

```
/** @var SitemapGenerator $sitemapGenerator */
$sitemapGenerator = resolve(Contracts\SitemapGenerator::class);
```

### Add definitions

```
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

```
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

```
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

```
$sitemapGenerator->deleteDefinition('example.com/new-url');
```

## Exclude URLs from sitemap

```
Event::listen(SitemapGenerator::EXCLUDE_URLS_EVENT, static function () {
    return [
        'example.com/page/1',
    ];
});
```

## Settings

You can change the amount of minutes the sitemap is cached in your `.env` file.
You can also cache the sitemap forever.

 ```
VDLP_SITEMAP_CACHE_TIME = 60
VDLP_SITEMAP_CACHE_FOREVER = false
```

## Issues

If you have issues using this plugin. Please create an issue on GitHub or contact us at [octobercms@vdlp.nl]().

## Contribution

Any help is appreciated. Or feel free to create a Pull Request on GitHub.
