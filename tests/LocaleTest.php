<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\I18n\Tests;

use PHPUnit\Framework\TestCase;
use UserFrosting\I18n\Locale;
use UserFrosting\I18n\LocaleInterface;
use UserFrosting\UniformResourceLocator\ResourceLocator;
use UserFrosting\Support\Exception\FileNotFoundException;

class LocaleTest extends TestCase
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

    public function testConstructor(): Locale
    {
        $locale = new Locale('fr_FR', 'locale://fr_FR/config.yaml');
        $this->assertInstanceOf(LocaleInterface::class, $locale);

        return $locale;
    }

    public function testConstructorWithNotFoundPath()
    {
        $this->expectException(FileNotFoundException::class);
        $locale = new Locale('fr_FR', 'locale://fr_FR/dontexist.yaml');
    }

    /**
     * @depends testConstructor
     */
    public function testGetConfigFile(Locale $locale)
    {
        $data = $locale->getConfigFile();
        $this->assertInternalType('string', $data);

        $this->assertSame('locale://fr_FR/config.yaml', $data);
    }

    /**
     * @depends testConstructor
     */
    public function testGetIndentifier(Locale $locale)
    {
        $data = $locale->getIndentifier();
        $this->assertInternalType('string', $data);

        $this->assertSame('fr_FR', $data);
    }

    /**
     * @depends testConstructor
     */
    public function testGetConfig(Locale $locale)
    {
        $data = $locale->getConfig();
        $this->assertInternalType('array', $data);

        $this->assertSame([
            'name'           => 'French',
            'localized_name' => 'Français',
            'authors'        => [
                'Foo Bar',
                'Bar Foo', // Not available in `core` version
            ],
            'options' => [
                'plural' => 2,
            ],
            'parents' => [
                'en_US'
            ]
        ], $data);
    }

    /**
     * @depends testConstructor
     * @depends testGetConfig
     */
    public function testGetAuthors(Locale $locale)
    {
        $data = $locale->getAuthors();
        $this->assertInternalType('array', $data);

        $this->assertSame([
            'Foo Bar',
            'Bar Foo', // Not available in `core` version
        ], $data);

        $this->assertSame($locale->getConfig()['authors'], $data);
    }

    /**
     * @depends testConstructor
     * @depends testGetConfig
     */
    public function testGetDetails(Locale $locale)
    {
        //getName
        $this->assertInternalType('string', $locale->getName());
        $this->assertSame('French', $locale->getName());

        //getLocalizedName
        $this->assertInternalType('string', $locale->getLocalizedName());
        $this->assertSame('Français', $locale->getLocalizedName());

        //getDependentLocales
        $this->assertInternalType('array', $locale->getDependentLocales());
        $this->assertSame(['en_US'], $locale->getDependentLocales());
    }

    /**
     * @depends testConstructor
     * @depends testGetConfig
     */
    /*public function testGetDictionary(Locale $locale)
    {
        $dictionary = $locale->getDictionary();
        $this->assertInternalType('array', $dictionary);


    }*/
}
