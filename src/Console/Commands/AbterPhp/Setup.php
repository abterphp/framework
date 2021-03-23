<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\AbterPhp;

use AbterPhp\Framework\Console\Commands\Oauth2\GenerateKeys;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class Setup extends Command
{
    protected const NAME = 'abterphp:setup';

    protected const DESCRIPTION = 'Setup Abterphp';

    /** @var string[] */
    protected array $subCommands = [
        /** @see GenerateKeys::doExecute() */
        GenerateKeys::NAME,
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
