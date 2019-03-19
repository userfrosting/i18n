<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\PluralRules;

/**
 * Families: Slavic (Slovak, Czech)
 * 1 - 1
 * 2 - 2, 3, 4
 * 3 - everything else: 0, 5, 6, 7, ...
 */
class Rule8 implements RuleInterface
{
    public static function getRule($number)
    {
        return ($number == 1) ? 1 : ((($number >= 2) && ($number <= 4)) ? 2 : 3);
    }
}
