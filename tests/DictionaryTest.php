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
use UserFrosting\Support\Repository\Loader\ArrayFileLoader;
use UserFrosting\UniformResourceLocator\Resource;
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

    public function testConstructor(): void
    {
        $locale = Mockery::mock(Locale::class);

        $dictionary = new Dictionary($locale, $this->locator);
        $this->assertInstanceOf(DictionaryInterface::class, $dictionary);
    }

    /**
     * @depends testConstructor
     */
    public function testGetDictionary_withNoDependentLocaleNoData(): void
    {
        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([]);
        $locale->shouldReceive('getIndentifier')->andReturn('aa_bb');

        // Prepare mock Locator - Return no file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->with('locale://aa_bb', true)->andReturn([]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldNotReceive('setPaths');
        $fileLoader->shouldNotReceive('load');

        // Set expectations
        $expectedResult = [];

        // Get dictionnary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withNoDependentLocaleNoData
     */
    public function testSetUri(): void
    {
        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([]);
        $locale->shouldReceive('getIndentifier')->andReturn('aa_bb');

        // Prepare mock Locator - Return no file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->with('foo://aa_bb', true)->andReturn([]);

        // Prepare mock FileLoader - No files, so loader shouldn't load anything
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldNotReceive('setPaths');
        $fileLoader->shouldNotReceive('load');

        // Set expectations
        $expectedResult = [];

        // Get dictionnary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $dictionary->setUri('foo://');
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withNoDependentLocaleNoData
     */
    public function testGetDictionary_withNoDependentLocaleWithData(): void
    {
        // Set expectations
        $expectedResult = ['Foo' => 'Bar'];

        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([]);
        $locale->shouldReceive('getIndentifier')->andReturn('aa_bb');

        // Prepare mock Resource - File `Foo/Bar/File1.php`
        $file = Mockery::mock(Resource::class);
        $file->shouldReceive('getExtension')->andReturn('php');
        $file->shouldReceive('__toString')->andReturn('Foo/Bar/File1.php');

        // Prepare mock Locator - Return the file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->with('locale://aa_bb', true)->andReturn([$file]);

        // Prepare mock FileLoader - Will return the mock file, with a mock data
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldReceive('setPaths')->with(['Foo/Bar/File1.php']);
        $fileLoader->shouldReceive('load')->andReturn($expectedResult);

        // Get dictionnary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @depends testGetDictionary_withNoDependentLocaleWithData
     */
    public function testGetDictionary_withNoDependentLocaleWithManyFiles(): void
    {
        // Set expectations
        $expectedResult = [
            'Foo'  => 'Bar',
            'Bar'  => 'Foo',
            'test' => [
                'Bar' => 'Rab',
                'Foo' => 'Oof',
            ],
        ];

        // Prepare mocked locale - aa_bb
        $locale = Mockery::mock(Locale::class);
        $locale->shouldReceive('getDependentLocales')->andReturn([]);
        $locale->shouldReceive('getIndentifier')->andReturn('aa_bb');

        // Prepare first mock Resource - File `Foo/Bar/File1.php`
        $file1 = Mockery::mock(Resource::class);
        $file1->shouldReceive('getExtension')->andReturn('php');
        $file1->shouldReceive('__toString')->andReturn('Foo/Bar/File1.php');

        // Prepare second mock Resource - File `Bar/Foo/File2.php`
        $file2 = Mockery::mock(Resource::class);
        $file2->shouldReceive('getExtension')->andReturn('php');
        $file2->shouldReceive('__toString')->andReturn('Bar/Foo/File2.php');

        // Prepare Third mock Resource - non `.php` file
        $file3 = Mockery::mock(Resource::class);
        $file3->shouldReceive('getExtension')->andReturn('txt');
        $file3->shouldNotReceive('__toString');

        // Prepare mock Locator - Return the file
        $locator = Mockery::mock(ResourceLocator::class);
        $locator->shouldReceive('listResources')->with('locale://aa_bb', true)->andReturn([$file1, $file2, $file3]);

        // Prepare mock FileLoader - Will return the mock file, with a mock data
        $fileLoader = Mockery::mock(ArrayFileLoader::class);
        $fileLoader->shouldReceive('setPaths')->with(['Foo/Bar/File1.php', 'Bar/Foo/File2.php']);
        $fileLoader->shouldReceive('load')->andReturn($expectedResult);

        // Get dictionnary
        $dictionary = new Dictionary($locale, $locator, $fileLoader);
        $data = $dictionary->getDictionary();

        // Perform assertions
        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }

    /**
     * Integration test with default
     *
     * @depends testConstructor
     */
    public function testGetDictionary_withRealLocale(): void
    {
        $locale = new Locale('es_ES', 'locale://es_ES/config.yaml');
        $dictionary = new Dictionary($locale, $this->locator);

        $expectedResult = [
            'X_CARS' => [
                1 => '{{plural}} coche',
                2 => '{{plural}} coches',
            ],
            'FOO' => 'BAR',
        ];

        $data = $dictionary->getDictionary();

        $this->assertIsArray($data);
        $this->assertEquals($expectedResult, $data);
    }
}
