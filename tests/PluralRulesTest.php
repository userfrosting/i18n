<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n;

use PHPUnit\Framework\TestCase;

class PluralRulesTest extends TestCase
{
    /** @var MessageTranslator */
    protected $translator;

    public function setUp()
    {
        $this->translator = new MessageTranslator();
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testGetPluralFormWithException()
    {
        $this->translator->getPluralForm(1, 132);
    }

    public function pluralFormProvider()
    {
    }
}
