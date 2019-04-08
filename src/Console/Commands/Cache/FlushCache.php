<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Cache;

use AbterPhp\Framework\Console\Commands\Assets\FlushCache as AssetsFlushCacheCommand;
use AbterPhp\Framework\Console\Commands\Authorization\FlushCache as AuthorizationFlushCacheCommand;
use AbterPhp\Framework\Console\Commands\Template\FlushCache as TemplateFlushCacheCommand;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Console\Commands\FlushFrameworkCacheCommand;

class FlushCache extends Command
{
    const NAME = 'abterphp:flushcache';

    const DESCRIPTION = 'Flushes all registered cache types';

    const OPULENCE_FRAMEWORK_FLUSHCACHE = 'framework:flushcache';

    /** @var array */
    protected $subCommands = [
        /** @see AssetsFlushCacheCommand::doExecute() */
        AssetsFlushCacheCommand::NAME,
        /** @see TemplateFlushCacheCommand::doExecute() */
        TemplateFlushCacheCommand::NAME,
        /** @see AuthorizationFlushCacheCommand::doExecute() */
        AuthorizationFlushCacheCommand::NAME,
        /** @see FlushFrameworkCacheCommand::doExecute() */
        self::OPULENCE_FRAMEWORK_FLUSHCACHE,
    ];

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName(static::NAME)
            ->setDescription(static::DESCRIPTION);
    }

    /**
     * @param string $subCommand
     */
    public function addSubCommand(string $subCommand)
    {
        $this->subCommands[] = $subCommand;
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
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
