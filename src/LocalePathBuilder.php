<?php
/**
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n;

use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;
use UserFrosting\Support\Repository\PathBuilder\PathBuilder;

/**
 * Globs together all files in specified locale(s) in each search path.
 *
 * @author Alexander Weissman (https://alexanderweissman.com)
 */
class LocalePathBuilder extends PathBuilder
{
    /**
     * @var string[]
     */
    protected $locales;

    /**
     * Create the loader.
     *
     * @param ResourceLocatorInterface $locator
     * @param string                   $uri     Scheme used to locate resources in $locator, including '://'
     * @param string|string[]          $locales A list of locale names (e.g. 'en_US'). Note that locale preference is ascending.
     */
    public function __construct(ResourceLocatorInterface $locator, $uri, $locales = [])
    {
        $this->setLocales($locales);

        parent::__construct($locator, $uri);
    }

    /**
     * Glob together all translation files for the current locales.
     *
     * @return string[]
     */
    public function buildPaths()
    {
        // Get all paths from the locator that match the uri.
        // Put them in reverse order to allow later files to override earlier files.
        $searchPaths = array_reverse($this->locator->findResources($this->uri, true, true));

        $filePaths = [];

        foreach ($this->locales as $locale) {
            // Make sure it's a valid string before loading
            if (is_string($locale) && $locale != '') {
                $localePaths = $this->buildLocalePaths($searchPaths, trim($locale));
                $filePaths = array_merge($filePaths, $localePaths);
            }
        }

        return $filePaths;
    }

    /**
     * Adds provides locales to the end of the current locales list.
     * Note that locale preference is ascending.
     *
     * @param  string|string[] $locales
     * @return $this
     */
    public function addLocales($locales = [])
    {
        //So we can accept strings argument also
        if (!is_array($locales)) {
            $locales = [$locales];
        }

        // Add the new locales to the end
        $this->locales = array_merge($this->locales, $locales);

        // Remove any duplicates, preserving only the last instance
        $this->locales = array_reverse(array_unique(array_reverse($this->locales)));

        return $this;
    }

    /**
     * Returns list of locales.
     *
     * @return string[]
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Sets locales.
     * Note that locale preference is ascending.
     *
     * @param string|string[] $locales
     */
    public function setLocales($locales = [])
    {
        $this->locales = [];
        $this->addLocales($locales);

        return $this;
    }

    /**
     * Construct paths to all locale files for a given locale.
     *
     * @param string[] $searchPaths
     * @param string   $locale
     */
    protected function buildLocalePaths($searchPaths, $locale)
    {
        $filePaths = [];

        // Search all paths for the specified locale
        foreach ($searchPaths as $path) {
            $localePath = rtrim($path, '/\\') . '/' . $locale;
            // Grab all php files in the locale directory
            $globs = glob($localePath . '/*.php');
            $filePaths = array_merge($filePaths, $globs);
        }

        return $filePaths;
    }
}
