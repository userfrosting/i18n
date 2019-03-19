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
 * Families: Icelandic
 * 1 - ends in 1, not 11: 1, 21, 31, ... 101, 121, 131, ...
 * 2 - everything else: 0, 2, 3, ... 10, 11, 12, ... 20, 22, ...
 */
class Rule15 implements RuleInterface
{
    public static function getRule($number)
    {
        return (($number % 10 == 1) && ($number % 100 != 11)) ? 1 : 2;
    }
}
