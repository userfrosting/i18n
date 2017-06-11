<?php

use PHPUnit\Framework\TestCase;
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;
use UserFrosting\I18n\LocalePathBuilder;

class LocalePathBuilderTest extends TestCase
{
    protected $basePath;

    protected $locator;

    public function setUp()
    {
        $this->basePath = __DIR__ . '/data';
        $this->locator = new UniformResourceLocator($this->basePath);

        // Add them one at a time to simulate how they are added in SprinkleManager
        $this->locator->addPath('locale', '', 'core/locale');
        $this->locator->addPath('locale', '', 'account/locale');
        $this->locator->addPath('locale', '', 'admin/locale');
    }

    /**
     * Test paths for a single locale.
     */
    public function testOne()
    {
        // Arrange
        $builder = new LocalePathBuilder($this->locator, 'locale://');

        // Act
        $paths = $builder->buildPaths('en_US');

        // Assert
        $this->assertEquals([
            $this->basePath . '/core/locale/en_US/readme.php',
            $this->basePath . '/core/locale/en_US/test.php',
            $this->basePath . '/core/locale/en_US/twig.php'
        ], $paths);
    }

    /**
     * Test paths for multiple locales.
     */
    public function testMany()
    {
        // Arrange
        $builder = new LocalePathBuilder($this->locator, 'locale://');

        // Act
        $paths = $builder->buildPaths(['en_US', 'fr_FR']);

        // Assert
        $this->assertEquals([
            $this->basePath . '/core/locale/en_US/readme.php',
            $this->basePath . '/core/locale/en_US/test.php',
            $this->basePath . '/core/locale/en_US/twig.php',
            $this->basePath . '/core/locale/fr_FR/test.php'
        ], $paths);
    }
}
