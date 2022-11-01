<?php

namespace Zenstruck\Metadata\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Zenstruck\Metadata\MapGenerator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 *
 * @codeCoverageIgnore
 */
final class Plugin implements PluginInterface, EventSubscriberInterface, Capable, CommandProvider
{
    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_AUTOLOAD_DUMP => 'postAutoloadDump',
        ];
    }

    public function postAutoloadDump(Event $event): void
    {
        // https://getcomposer.org/doc/articles/plugins.md#plugin-autoloading
        require $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        $extra = $event->getComposer()->getPackage()->getExtra();

        // use root package's psr-4 autoload path if not set explicitly
        $paths = $extra['class-metadata']['paths'] ?? $event->getComposer()->getPackage()->getAutoload()['psr-4'] ?? [];

        if (!\is_array($paths)) {
            // disable path scanning if set to something other than an array (false)
            $paths = [];
        }

        MapGenerator::generate($paths, $extra['class-metadata']['map'] ?? []);

        $event->getIO()->write('<info>Generated class-metadata map.</>');
    }

    public function activate(Composer $composer, IOInterface $io): void
    {
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
        MapGenerator::removeFile();
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public function getCapabilities(): array
    {
        return [CommandProvider::class => self::class];
    }

    public function getCommands(): array
    {
        return [new ListMetadataCommand()];
    }
}
