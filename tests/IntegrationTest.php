<?php

namespace Zenstruck\Metadata\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IntegrationTest extends TestCase
{
    use InteractsWithGeneratedMap;

    /**
     * @before
     * @after
     */
    public static function cleanup(): void
    {
        (new Filesystem())->remove(\glob(__DIR__.'/Fixture/projects/*/vendor'));
    }

    /**
     * @test
     * @dataProvider composerAutoloadArgProvider
     */
    public function composer_install_and_dump_autoload_standard_project(string $arg): void
    {
        $cwd = __DIR__.'/Fixture/projects/project1';

        $output = $this->composer(['install', $arg], $cwd);
        $this->assertStringContainsString('Generated class-metadata map.', $output);

        $output = $this->composer(['list-class-metadata'], $cwd);
        $this->assertStringContainsString('App\ClassB                     class-b       []', $output);
        $this->assertStringContainsString('App\Sub\ClassC                 class-c       {"foo":"bar"}', $output);
        $this->assertStringContainsString('Zenstruck\Assert               assert        []', $output);
        $this->assertStringContainsString('Zenstruck\Assert\Expectation   expectation   {"key1":"value","key2":7}', $output);

        $output = $this->composer(['dump-autoload', $arg], $cwd);
        $this->assertStringContainsString('Generated class-metadata map.', $output);

        $output = $this->composer(['list-class-metadata'], $cwd);
        $this->assertStringContainsString('App\ClassB                     class-b       []', $output);
        $this->assertStringContainsString('App\Sub\ClassC                 class-c       {"foo":"bar"}', $output);
        $this->assertStringContainsString('Zenstruck\Assert               assert        []', $output);
        $this->assertStringContainsString('Zenstruck\Assert\Expectation   expectation   {"key1":"value","key2":7}', $output);
    }

    public static function composerAutoloadArgProvider(): iterable
    {
        yield [''];
        yield ['-o']; // optimize
        yield ['-a']; // classmap-authoritative
    }

    /**
     * @test
     */
    public function can_disable_namespace_scanning(): void
    {
        $cwd = __DIR__.'/Fixture/projects/project2';

        $output = $this->composer(['install'], $cwd);
        $this->assertStringContainsString('Generated class-metadata map.', $output);

        $output = $this->composer(['list-class-metadata'], $cwd);
        $this->assertStringNotContainsString('App\ClassB                     class-b       []', $output);
        $this->assertStringNotContainsString('App\Sub\ClassC                 class-c       {"foo":"bar"}', $output);
        $this->assertStringContainsString('Zenstruck\Assert               assert        []', $output);
        $this->assertStringContainsString('Zenstruck\Assert\Expectation   expectation   {"key1":"value","key2":7}', $output);
    }

    /**
     * @test
     */
    public function can_specify_specific_namespaces_to_scan(): void
    {
        $cwd = __DIR__.'/Fixture/projects/project3';

        $output = $this->composer(['install'], $cwd);
        $this->assertStringContainsString('Generated class-metadata map.', $output);

        $output = $this->composer(['list-class-metadata'], $cwd);
        $this->assertStringNotContainsString('App\ClassB                     class-b       []', $output);
        $this->assertStringContainsString('App\Sub\ClassC                 class-c       {"foo":"bar"}', $output);
        $this->assertStringContainsString('Zenstruck\Assert               assert        []', $output);
        $this->assertStringContainsString('Zenstruck\Assert\Expectation   expectation   {"key1":"value","key2":7}', $output);
    }

    private function composer(array $args, string $cwd): string
    {
        $process = new Process(\array_filter([(new ExecutableFinder())->find('composer'), ...$args]), $cwd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
