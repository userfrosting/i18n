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
 * Families: Semitic (Arabic)
 * 1 - 1
 * 2 - 2
 * 3 - ends in 03-10: 3, 4, ... 10, 103, 104, ... 110, 203, 204, ...
 * 4 - ends in 11-99: 11, ... 99, 111, 112, ...
 * 5 - everything else: 100, 101, 102, 200, 201, 202, ...
 * 6 - 0
 */
class Rule12 implements RuleInterface
{
    public static function getRule($number)
    {
        return ($number == 1) ? 1 : (($number == 2) ? 2 : ((($number % 100 >= 3) && ($number % 100 <= 10)) ? 3 : ((($number % 100 >= 11) && ($number % 100 <= 99)) ? 4 : (($number != 0) ? 5 : 6))));
    }
}
