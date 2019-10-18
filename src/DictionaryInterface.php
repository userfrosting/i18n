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
 * Locale Dictionnary
 *
 * Used to return all "Key => translation" data matrix
 *
 * @author Louis Charette
 */
interface DictionaryInterface
{
    /**
     * Returns all loaded locale Key => Translation data dictionary.
     *
     * @return string[] The locale dictionnary
     */
    public function getDictionary(): array;
}
