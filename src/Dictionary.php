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
     * @var array Locale "Key => translation" data matrix cache
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
     * Return the associate locale
     *
     * @return LocaleInterface
     */
    public function getLocale(): LocaleInterface
    {
        return $this->locale;
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
        $dictionary = [];

        // Get list of files to load
        $files = $this->getFiles();
        $files = $this->filterDictionaryFiles($files);

        // Load all files content if files are present
        if (!empty($files)) {
            $loader = $this->getFileLoader();
            $loader->setPaths($files);

            $dictionary = $loader->load();
        }

        // Now load dependent dictionnaries
        foreach ($this->locale->getDependentLocales() as $locale) {
            $dependentDictionary = new self($locale, $this->locator, $this->fileLoader);
            $dictionary = array_merge_recursive($dependentDictionary->getDictionary(), $dictionary);
        }

        return $dictionary;
    }

    /**
     * Remove config files from locator results and convert ResourceInterface to path/string.
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
     * @return \UserFrosting\UniformResourceLocator\ResourceInterface[]
     */
    protected function getFiles(): array
    {
        return $this->locator->listResources($this->uri.$this->locale->getIndentifier(), true);
    }
}
