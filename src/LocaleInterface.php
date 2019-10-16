<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n;

/**
 * Locale interface.
 *
 * @author Louis Charette
 */
interface LocaleInterface
{
    /**
     * Create locale class
     *
     * @param string $identifier The locale identifier (ie. "en_US")
     * @param string $configFile The path to the locale config file
     */
    public function __construct(string $identifier, string $configFile);

    /**
     * Returns the list of authors of the locale
     *
     * @return string[] The list of authors
     */
    public function getAuthors(): array;

    /**
     * Returns all loaded locale Key => Translation data dictionary
     *
     * @return string[] The locale dictionnary
     */
    public function getDictionary(): array;

    /**
     * Returns defined configuration file
     *
     * @return string
     */
    public function getConfigFile(): string;

    /**
     * Returns the locale indentifier
     *
     * @return string
     */
    public function getIndentifier(): string;

    /**
     * Return the raw configuration data
     *
     * @return array
     */
    public function getConfig(): array;
}
