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
use UserFrosting\Support\Repository\Loader\FileRepositoryLoader;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Locale Dictionnary.
 *
 * Load all locale all "Key => translation" data matrix
 *
 * @author Louis Charette
 */
class Dictionary implements DictionaryInterface
{
    /**
     * @var string Base URI for locator
     */
    protected $uri = 'locale://';

    /**
     * @var LocaleInterface
     */
    protected $locale;

    /**
     * @var ResourceLocatorInterface
     */
    protected $locator;

    /**
     * @var FileRepositoryLoader
     */
    protected $fileLoader;

    /**
     * @var array Locale "Key => translation" data matrix
     */
    protected $dictionary = [];

    /**
     * @param LocaleInterface          $locale
     * @param ResourceLocatorInterface $locator
     * @param FileRepositoryLoader     $fileLoader File loader used to load each dictionnay files (default to Array Loader)
     */
    public function __construct(LocaleInterface $locale, ResourceLocatorInterface $locator, FileRepositoryLoader $fileLoader = null)
    {
        $this->locale = $locale;
        $this->locator = $locator;
        $this->fileLoader = is_null($fileLoader) ? new ArrayFileLoader([]) : $fileLoader;
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
     * Set the locator base URI (default 'locale://').
     *
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * Return the file repository loader used to load.
     *
     * @return FileRepositoryLoader
     */
    public function getFileLoader(): FileRepositoryLoader
    {
        return $this->fileLoader;
    }

    /**
     * Load the dictionnary from file.
     *
     * @return (string|array)[] The locale dictionnary
     */
    protected function loadDictionary(): array
    {
        // Get list of files to load
        $files = $this->getDictionaryFiles();

        // Stop if no files are present
        if (empty($files)) {
            return [];
        }

        // Load all files content
        $loader = $this->getFileLoader();
        $loader->setPaths($files);

        return $loader->load();
    }

    /**
     * Returns a list of files to load.
     *
     * @return string[]
     */
    protected function getDictionaryFiles(): array
    {
        $files = [];

        // First, load all parents locales
        $parents = $this->locale->getDependentLocales();

        if (!empty($parents)) {
            foreach ($parents as $parent) {
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
     * Remove config files from locator results and convert ResourceInterface to path/string
     *
     * @param \UserFrosting\UniformResourceLocator\ResourceInterface[] $files
     *
     * @return string[]
     */
    protected function filterDictionaryFiles(array $files): array
    {
        return array_filter($files, function ($file) {
            if ($file->getExtension() == 'php') {
                return (string) $file;
            }
        });
    }

    /**
     * List all files for a given locale using the locator.
     *
     * @param LocaleInterface $locale
     *
     * @return \UserFrosting\UniformResourceLocator\ResourceInterface[]
     */
    protected function getFilesForLocale(LocaleInterface $locale): array
    {
        return $this->locator->listResources($this->uri.$locale->getIndentifier(), true);
    }
}
