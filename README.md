# Nova Translations
Translation Tool for Nova

## Installation
```
composer require agj/nova-translations
```

Insert the following line in the `App\Providers\NovaServiceProvider`:
```
use Agj\NovaTranslations\NovaTranslations;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
   ...
   
   /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new NovaTranslations,
        ];
    }
    ...
}
```

And in the `config/app.php` `providers` array:
```
    Agj\NovaTranslations\TranslationServiceProvider::class,
```
