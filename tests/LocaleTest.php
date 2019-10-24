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
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\UniformResourceLocator\ResourceLocator;

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
        $locale = new Locale('fr_FR', 'locale://fr_FR/locale.yaml');
        $this->assertInstanceOf(LocaleInterface::class, $locale);

        return $locale;
    }

    public function testConstructorWithNotFoundPath(): void
    {
        $this->expectException(FileNotFoundException::class);
        $locale = new Locale('fr_FR', 'locale://fr_FR/dontexist.yaml');
    }

    /**
     * @depends testConstructor
     */
    public function testGetConfigFile(Locale $locale): void
    {
        $data = $locale->getConfigFile();
        $this->assertIsString($data);

        $this->assertSame('locale://fr_FR/locale.yaml', $data);
    }

    /**
     * @depends testGetConfigFile
     */
    public function testConstructorWithNotPath(): void
    {
        $locale = new Locale('fr_FR');
        $this->assertInstanceOf(LocaleInterface::class, $locale);
        $this->assertSame('locale://fr_FR/locale.yaml', $locale->getConfigFile());
    }

    /**
     * @depends testConstructor
     */
    public function testGetIndentifier(Locale $locale): void
    {
        $data = $locale->getIndentifier();
        $this->assertIsString($data);

        $this->assertSame('fr_FR', $data);
    }

    /**
     * @depends testConstructor
     */
    public function testGetConfig(Locale $locale): void
    {
        $data = $locale->getConfig();
        $this->assertIsArray($data);

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
                'en_US',
            ],
        ], $data);
    }

    /**
     * @depends testConstructor
     * @depends testGetConfig
     */
    public function testGetAuthors(Locale $locale): void
    {
        $data = $locale->getAuthors();
        $this->assertIsArray($data);

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
    public function testGetDetails(Locale $locale): void
    {
        //getName
        $this->assertIsString($locale->getName());
        $this->assertSame('French', $locale->getName());

        //getLocalizedName
        $this->assertIsString($locale->getLocalizedName());
        $this->assertSame('Français', $locale->getLocalizedName());

        //getDependentLocalesIdentifier
        $this->assertIsArray($locale->getDependentLocalesIdentifier());
        $this->assertSame(['en_US'], $locale->getDependentLocalesIdentifier());

        //getPluralRule
        $this->assertIsInt($locale->getPluralRule());
        $this->assertSame(2, $locale->getPluralRule());
    }

    /**
     * @depends testConstructor
     * @depends testGetConfig
     */
    public function testGetPluralRule(Locale $locale): void
    {
        $this->assertIsInt($locale->getPluralRule());
        $this->assertSame(2, $locale->getPluralRule());
    }

    /**
     * @depends testConstructorWithNotPath
     * @depends testGetPluralRule
     */
    public function testGetPluralRuleWithNoRule(): void
    {
        $locale = new Locale('es_ES');
        $this->assertIsInt($locale->getPluralRule());
        $this->assertSame(1, $locale->getPluralRule());
    }

    /**
     * @depends testConstructor
     * @depends testGetDetails
     */
    public function testGetDependentLocales(Locale $locale): void
    {
        $result = $locale->getDependentLocales();
        $this->assertIsArray($result);
        $this->assertInstanceOf(LocaleInterface::class, $result[0]);
    }

    /*
     * @depends testConstructor
     * @depends testGetConfig
     */
    /*public function testGetDictionary(Locale $locale)
    {
        $dictionary = $locale->getDictionary();
        $this->assertInternalType('array', $dictionary);


    }*/

    /*
    TODO :
        - fr_CA
        - Null Parent
     */
}
