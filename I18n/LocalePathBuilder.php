<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 */
namespace UserFrosting\I18n;

use UserFrosting\Support\Repository\PathBuilder\PathBuilder;

/**
 * Globs together all files in specified locale(s) in each search path.
 */
class LocalePathBuilder extends PathBuilder
{
    /**
     * Glob together all translation files for zero or more locales.
     *
     * @param string $locales A list of locale names (e.g. 'en_US')
     * @return array
     */
    public function buildPaths($locales = [])
    {
        //So we can accept strings argument also
        if (!is_array($locales)) {
            $locales = array($locales);
        }

        // Get all paths from the locator that match the uri.
        // Put them in reverse order to allow later files to override earlier files.
        $searchPaths = array_reverse($this->locator->findResources($this->uri, true, true));

        $filePaths = [];

        foreach ($locales as $locale) {
            // Make sure it's a valid string before loading
            if (is_string($locale) && $locale != "") {
                $localePaths = $this->buildLocalePaths($searchPaths, trim($locale));
                $filePaths = array_merge($filePaths, $localePaths);
            }
        }

        return $filePaths;
    }

    /**
     * Construct paths to all locale files for a given locale.
     *
     * @param string[] $searchPaths
     * @param string $locale
     */
    protected function buildLocalePaths($searchPaths, $locale)
    {
        $filePaths = [];

        // Search all paths for the specified locale
        foreach ($searchPaths as $path) {
            $localePath = rtrim($path, '/\\') . '/' . $locale;
            // Grab all php files in the locale directory
            $globs = glob($localePath . "/*.php");
            $filePaths = array_merge($filePaths, $globs);
        }
        
        return $filePaths;
    }
}
