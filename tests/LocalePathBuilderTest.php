<?php

/*
 * UserFrosting i18n (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/i18n
 * @copyright Copyright (c) 2013-2019 Alexander Weissman, Louis Charette
 * @license   https://github.com/userfrosting/i18n/blob/master/LICENSE.md (MIT License)
 */

use PHPUnit\Framework\TestCase;
use UserFrosting\I18n\LocalePathBuilder;
use UserFrosting\UniformResourceLocator\ResourceLocator;

class LocalePathBuilderTest extends TestCase
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

    /**
     * Test paths for a single locale.
     */
    public function testOne()
    {
        // Arrange
        $builder = new LocalePathBuilder($this->locator, 'locale://', 'en_US');

        // Act
        $paths = $builder->buildPaths();

        // Assert
        $this->assertEquals([
            $this->basePath.'/core/locale/en_US/readme.php',
            $this->basePath.'/core/locale/en_US/test.php',
            $this->basePath.'/core/locale/en_US/twig.php',
        ], $paths);
    }

    /**
     * Test paths for multiple locales.
     */
    public function testMany()
    {
        // Arrange
        $builder = new LocalePathBuilder($this->locator, 'locale://', 'en_US');
        $builder->addLocales('fr_FR');

        // Act
        $paths = $builder->buildPaths();

        // Assert
        $this->assertEquals([
            $this->basePath.'/core/locale/en_US/readme.php',
            $this->basePath.'/core/locale/en_US/test.php',
            $this->basePath.'/core/locale/en_US/twig.php',
            $this->basePath.'/core/locale/fr_FR/test.php',
        ], $paths);
    }

    /**
     * Test locale values.
     */
    public function testInitLocales()
    {
        // Arrange
        $builder = new LocalePathBuilder($this->locator, 'locale://');
        $builder->addLocales('en_US');

        // Act
        $locales = $builder->getLocales();

        // Assert
        $this->assertEquals(['en_US'], $locales);
    }

    public function testRepeatLocales()
    {
        // Arrange
        $builder = new LocalePathBuilder($this->locator, 'locale://', ['en_US', 'fr_FR', 'fr_FR', 'en_US', 'fr_FR']);
        $builder->addLocales(['en_US', 'en_US']);

        // Act
        $locales = $builder->getLocales();

        // Assert
        $this->assertEquals(['fr_FR', 'en_US'], $locales);
    }
}
