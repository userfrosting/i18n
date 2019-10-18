<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n;

use UserFrosting\Support\Repository\Loader\ArrayFileLoader;
use UserFrosting\I18n\LocaleInterface;
use UserFrosting\UniformResourceLocator\Resource;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Locale Dictionnary
 *
 * Load all locale all "Key => translation" data matrix
 *
 * @author Louis Charette
 */
class Dictionary implements DictionaryInterface
{
    /**
     * @var LocaleInterface
     */
    protected $locale;

    /**
     * @var ResourceLocatorInterface
     */
    protected $locator;

    /**
     * @var array Locale "Key => translation" data matrix
     */
    protected $dictionary = [];

    /**
     * @param LocaleInterface $locale
     * @param ResourceLocatorInterface $locator
     */
    public function __construct(LocaleInterface $locale, ResourceLocatorInterface $locator)
    {
        $this->locale = $locale;
        $this->locator = $locator;
    }

    /**
     * Returns all loaded locale Key => Translation data dictionary.
     * Won't load the whole thing twice if already loaded in the class.
     *
     * @return string[] The locale dictionnary
     */
    public function getDictionary(): array
    {
        if (empty($this->dictionary)) {
            $this->dictionary = $this->loadDictionary();
        }

        return $this->dictionary;
    }

    /**
     * Load the dictionnary from file
     *
     * @return array The locale dictionnary
     */
    protected function loadDictionary(): array
    {
        // Get list of files to load
        $files = $this->getDictionaryFiles();

        // Load all files content
        $loader = new ArrayFileLoader($files);
        return $loader->load();
    }

    /**
     * Returns a list of files to load
     * @return array[Resource]
     */
    protected function getDictionaryFiles(): array
    {
        $files = [];

        // First, load all parents locales
        $parents = $this->locale->getDependentLocales();

        if (!empty($parents)) {
            foreach ($parents as $parent)
            {
                $parentLocale = new Locale($parent, "locale://$parent/config.yaml");
                //TODO : Recursively load dictionnary instead, cause a dependant can have dependencies
                $files = array_merge($files, $this->getFilesForLocale($parentLocale));
            }
        }

        // Now get for main locale
        $files = array_merge($files, $this->getFilesForLocale($this->locale));

        // Only keep .php files
        return $this->filterDictionaryFiles($files);
    }

    /**
     * Remove config files from locator results
     *
     * @param  array  $files
     * @return array[Resource]
     */
    protected function filterDictionaryFiles(array $files): array
    {
        //TODO : Use Array_filter

        $filtered = [];

        foreach ($files as $file) {
            if ($file->getExtension() == "php") {
                $filtered[] = $file;
            }
        }

        return $filtered;
    }

    /**
     * List all files for a given locale using the locator
     *
     * @param  LocaleInterface $locale
     * @return array[Resource]
     */
    protected function getFilesForLocale(LocaleInterface $locale): array
    {
        return $this->locator->listResources('locale://' . $locale->getIndentifier(), true);
    }
}
