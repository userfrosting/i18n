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
 * Families: Slavic (Macedonian)
 * 1 - ends in 1: 1, 11, 21, ...
 * 2 - ends in 2: 2, 12, 22, ...
 * 3 - everything else: 0, 3, 4, ... 10, 13, 14, ... 20, 23, ...
 */
class Rule14 implements RuleInterface
{
    public static function getRule($number)
    {
        return ($number % 10 == 1) ? 1 : (($number % 10 == 2) ? 2 : 3);
    }
}
