<?php

namespace Kirby\Cache;

use Kirby\Toolkit\Dir;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

require_once __DIR__ . '/mocks.php';

/**
 * @coversDefaultClass \Kirby\Cache\FileCache
 */
class FileCacheTest extends TestCase
{
    public function tearDown(): void
    {
        Dir::remove(__DIR__ . '/fixtures/file');
    }

    /**
     * @covers ::__construct
     * @covers ::root
     */
    public function testConstruct()
    {
        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file'
        ]);

        $this->assertSame($root, $cache->root());
        $this->assertDirectoryExists($root);
    }

    /**
     * @covers ::__construct
     * @covers ::root
     */
    public function testConstructWithPrefix()
    {
        $cache = new FileCache([
            'root'   => $root = __DIR__ . '/fixtures/file',
            'prefix' => 'test'
        ]);

        $this->assertSame($root . '/test', $cache->root());
        $this->assertDirectoryExists($root . '/test');
    }

    /**
     * @covers ::file
     */
    public function testFile()
    {
        $method = new ReflectionMethod(FileCache::class, 'file');
        $method->setAccessible(true);

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file'
        ]);
        $this->assertSame($root . '/test', $method->invoke($cache, 'test'));

        $cache = new FileCache([
            'root'      => $root = __DIR__ . '/fixtures/file',
            'extension' => 'cache'
        ]);
        $this->assertSame($root . '/test.cache', $method->invoke($cache, 'test'));

        $cache = new FileCache([
            'root'   => $root = __DIR__ . '/fixtures/file',
            'prefix' => 'test1'
        ]);
        $this->assertSame($root . '/test1/test', $method->invoke($cache, 'test'));

        $cache = new FileCache([
            'root'      => $root = __DIR__ . '/fixtures/file',
            'prefix'    => 'test1',
            'extension' => 'cache'
        ]);
        $this->assertSame($root . '/test1/test.cache', $method->invoke($cache, 'test'));

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame($root . '/_empty/test', $method->invoke($cache, '/test'));

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame($root . '/test/_empty', $method->invoke($cache, 'test/'));

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame($root . '/test/_backslash/foo/bar', $method->invoke($cache, 'test\\foo/bar'));

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame($root . '/test/_backslash/_empty/foo/_backslash/bar', $method->invoke($cache, 'test\\/foo\\bar'));

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame($root . '/_empty/test/_empty', $method->invoke($cache, '/test/'));

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame(
            $root . '/_9d891e731f75deae56884d79e9816736b7488080/_9d891e731f75deae56884d79e9816736b7488080/test',
            $method->invoke($cache, '../../test')
        );

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame(
            $root . '/_9d891e731f75deae56884d79e9816736b7488080/test-cache_4caff0c1d0c8eb128ed9896b4b0258ef2848816b',
            $method->invoke($cache, '../test.cache')
        );

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame($root . '/_3a52ce780950d4d969792a2559cd519d7ee8c727/test-page', $method->invoke($cache, './test-page'));

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame(
            $root . '/_3a52ce780950d4d969792a2559cd519d7ee8c727/test-cache_4caff0c1d0c8eb128ed9896b4b0258ef2848816b',
            $method->invoke($cache, './test.cache')
        );

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame(
            $root . '/_9d891e731f75deae56884d79e9816736b7488080/pages/test/_empty',
            $method->invoke($cache, '../pages/test/')
        );

        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
        ]);
        $this->assertSame(
            $root . '/_9d891e731f75deae56884d79e9816736b7488080/pages/test-cache_4caff0c1d0c8eb128ed9896b4b0258ef2848816b',
            $method->invoke($cache, '../pages/test.cache')
        );

        $cache = new FileCache([
            'root'      => $root = __DIR__ . '/fixtures/file',
            'extension' => 'cache'
        ]);
        $this->assertSame(
            $root . '/_9d891e731f75deae56884d79e9816736b7488080/pages/test.cache',
            $method->invoke($cache, '../pages/test')
        );

        $cache = new FileCache([
            'root'   => $root = __DIR__ . '/fixtures/file',
            'prefix' => 'prefix'
        ]);
        $this->assertSame(
            $root . '/prefix/_9d891e731f75deae56884d79e9816736b7488080/pages/test',
            $method->invoke($cache, '../pages/test')
        );

        $cache = new FileCache([
            'root'      => $root = __DIR__ . '/fixtures/file',
            'prefix'    => 'prefix',
            'extension' => 'cache'
        ]);
        $this->assertSame(
            $root . '/prefix/_9d891e731f75deae56884d79e9816736b7488080/pages/test.cache',
            $method->invoke($cache, '../pages/test')
        );
        $this->assertSame(
            $root . '/prefix/_9d891e731f75deae56884d79e9816736b7488080/pages/test-invalid_76a6bcc476cffdcb56321fbbb4edfd19fece82c6.cache',
            $method->invoke($cache, '../pages/test.invalid')
        );
    }

    /**
     * @covers ::set
     * @covers ::created
     * @covers ::retrieve
     * @covers ::remove
     */
    public function testOperations()
    {
        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file'
        ]);

        $time = time();
        $this->assertTrue($cache->set('foo', 'A basic value', 10));
        touch($root . '/foo', $time);

        $this->assertFileExists($root . '/foo');
        $this->assertTrue($cache->exists('foo'));
        $this->assertSame('A basic value', $cache->retrieve('foo')->value());
        $this->assertSame($time, $cache->created('foo'));
        $this->assertSame($time + 600, $cache->expires('foo'));

        $this->assertTrue($cache->remove('foo'));
        $this->assertFileDoesNotExist($root . '/foo');
        $this->assertFalse($cache->exists('foo'));
        $this->assertNull($cache->retrieve('foo'));

        $this->assertFalse($cache->remove('doesnotexist'));
    }

    /**
     * @covers ::set
     * @covers ::created
     * @covers ::retrieve
     * @covers ::remove
     */
    public function testOperationsWithExtension()
    {
        $cache = new FileCache([
            'root'      => $root = __DIR__ . '/fixtures/file',
            'extension' => 'cache'
        ]);

        $time = time();
        $this->assertTrue($cache->set('foo', 'A basic value', 10));
        touch($root . '/foo.cache', $time);

        $this->assertFileExists($root . '/foo.cache');
        $this->assertTrue($cache->exists('foo'));
        $this->assertSame('A basic value', $cache->retrieve('foo')->value());
        $this->assertSame($time, $cache->created('foo'));
        $this->assertSame($time + 600, $cache->expires('foo'));

        $this->assertTrue($cache->remove('foo'));
        $this->assertFileDoesNotExist($root . '/foo.cache');
        $this->assertFalse($cache->exists('foo'));
        $this->assertNull($cache->retrieve('foo'));
    }

    /**
     * @covers ::set
     * @covers ::created
     * @covers ::retrieve
     * @covers ::remove
     */
    public function testOperationsWithPrefix()
    {
        $cache1 = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
            'prefix' => 'test1'
        ]);
        $cache2 = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
            'prefix' => 'test2'
        ]);

        $time = time();
        $this->assertTrue($cache1->set('foo', 'A basic value', 10));
        touch($root . '/test1/foo', $time);

        $this->assertFileExists($root . '/test1/foo');
        $this->assertTrue($cache1->exists('foo'));
        $this->assertFalse($cache2->exists('foo'));
        $this->assertSame('A basic value', $cache1->retrieve('foo')->value());
        $this->assertSame($time, $cache1->created('foo'));
        $this->assertSame($time + 600, $cache1->expires('foo'));

        $this->assertTrue($cache2->set('foo', 'Another basic value'));
        touch($root . '/test2/foo', $time);
        $this->assertTrue($cache2->exists('foo'));

        $this->assertSame('A basic value', $cache1->retrieve('foo')->value());
        $this->assertTrue($cache1->remove('foo'));
        $this->assertFileDoesNotExist($root . '/test1/foo');
        $this->assertFalse($cache1->exists('foo'));
        $this->assertNull($cache1->retrieve('foo'));
        $this->assertTrue($cache2->exists('foo'));
        $this->assertSame('Another basic value', $cache2->retrieve('foo')->value());
    }

    /**
     * @covers ::flush
     */
    public function testFlush()
    {
        $cache = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file'
        ]);

        $cache->set('a', 'A basic value');
        $cache->set('b', 'A basic value');
        $cache->set('c', 'A basic value');
        $this->assertFileExists($root . '/a');
        $this->assertFileExists($root . '/b');
        $this->assertFileExists($root . '/c');

        $this->assertTrue($cache->flush());
        $this->assertFileDoesNotExist($root . '/a');
        $this->assertFileDoesNotExist($root . '/b');
        $this->assertFileDoesNotExist($root . '/c');
    }

    /**
     * @covers ::flush
     */
    public function testFlushWithPrefix()
    {
        $cache1 = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
            'prefix' => 'test1'
        ]);
        $cache2 = new FileCache([
            'root' => $root = __DIR__ . '/fixtures/file',
            'prefix' => 'test2'
        ]);

        $cache1->set('a', 'A basic value');
        $cache1->set('b', 'A basic value');
        $cache2->set('a', 'A basic value');
        $cache2->set('b', 'A basic value');
        $this->assertFileExists($root . '/test1/a');
        $this->assertFileExists($root . '/test1/b');
        $this->assertFileExists($root . '/test2/a');
        $this->assertFileExists($root . '/test2/b');

        $this->assertTrue($cache1->flush());
        $this->assertFileDoesNotExist($root . '/test1/a');
        $this->assertFileDoesNotExist($root . '/test1/b');
        $this->assertFileExists($root . '/test2/a');
        $this->assertFileExists($root . '/test2/b');
    }
}
