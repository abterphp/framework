<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\AbterPhp;

use AbterPhp\Framework\Console\Commands\Assets\FlushCache as AssetsFlushCacheCommand;
use AbterPhp\Framework\Console\Commands\Authorization\FlushCache as AuthorizationFlushCacheCommand;
use AbterPhp\Framework\Console\Commands\Template\FlushCache as TemplateFlushCacheCommand;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class FlushCache extends Command
{
    protected const NAME = 'abterphp:flushcache';

    protected const DESCRIPTION = 'Flushes all registered cache types';

    protected const OPULENCE_FRAMEWORK_FLUSHCACHE = 'framework:flushcache';

    /** @var string[] */
    protected array $subCommands = [
        /** @see AssetsFlushCacheCommand::doExecute() */
        AssetsFlushCacheCommand::NAME,
        /** @see TemplateFlushCacheCommand::doExecute() */
        TemplateFlushCacheCommand::NAME,
        /** @see AuthorizationFlushCacheCommand::doExecute() */
        AuthorizationFlushCacheCommand::NAME,
        /** @see \Opulence\Framework\Console\Commands\FlushFrameworkCacheCommand::doExecute() */
        self::OPULENCE_FRAMEWORK_FLUSHCACHE,
    ];

    /**
     * @inheritdoc
     */
    protected function define(): void
    {
        $this->setName(static::NAME)
            ->setDescription(static::DESCRIPTION);
    }

    /**
     * @param string $subCommand
     */
    public function addSubCommand(string $subCommand): void
    {
        $this->subCommands[] = $subCommand;
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response): void
    {
        foreach ($this->subCommands as $subCommand) {
            try {
                $this->commandCollection->call($subCommand, $response);
            } catch (\Exception $e) {
                $response->writeln(sprintf('<error>%s@execute failed</error>', $subCommand));
                $response->writeln(sprintf('<fatal>%s</fatal>', $e->getMessage()));
            }
        }
    }
}
