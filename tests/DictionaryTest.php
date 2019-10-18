<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;
use UserFrosting\I18n\Dictionary;
use UserFrosting\I18n\DictionaryInterface;
use UserFrosting\I18n\Locale;
use UserFrosting\I18n\LocaleInterface;
use UserFrosting\UniformResourceLocator\ResourceLocator;

class DictionaryTest extends TestCase
{
    protected $basePath;

    protected $locator;

    public function setUp()
    {
        $this->basePath = __DIR__.'/data';
        $this->locator = new ResourceLocator($this->basePath);

        $this->locator->registerStream('locale');

        // Add them one at a time to simulate how they are added in SprinkleManager
        $this->locator->registerLocation('core');
        $this->locator->registerLocation('account');
        $this->locator->registerLocation('admin');
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testConstructor()
    {
        /**  @var LocaleInterface */
        $locale = Mockery::mock(Locale::class);

        $dictionary = new Dictionary($locale, $this->locator);
        $this->assertInstanceOf(DictionaryInterface::class, $dictionary);
    }

    /**
     * @depends testConstructor
     */
    /*public function testGetDictionary_withNoDependentLocaleNoData()
    {
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([]);

        $dictionary = new Dictionary($locale, $this->locator);

        $this->assertInternalType('array', $dictionary->getDictionary());
        $this->assertEquals('array', $dictionary->getDictionary());
    }*/

    /**
     * @depends testConstructor
     */
    public function testGetDictionary_withRealLocale()
    {
        $locale = new Locale('es_ES', 'locale://es_ES/config.yaml');
        $dictionary = new Dictionary($locale, $this->locator);

        $expectedResult = [
            'X_CARS' => [
                1 => '{{plural}} coche',
                2 => '{{plural}} coches',
            ],
            'FOO' => 'BAR'
        ];

        $data = $dictionary->getDictionary();

        $this->assertInternalType('array', $data);
        $this->assertEquals($expectedResult, $data);
    }
}
