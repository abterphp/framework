<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Console\Commands\Security\SecretGenerator;

class SecretGeneratorReady
{
    private SecretGenerator $secretGenerator;

    /**
     * SecretGeneratorReady constructor.
     *
     * @param SecretGenerator $secretGenerator
     */
    public function __construct(SecretGenerator $secretGenerator)
    {
        $this->secretGenerator = $secretGenerator;
    }

    /**
     * @return SecretGenerator
     */
    public function getSecretGenerator(): SecretGenerator
    {
        return $this->secretGenerator;
    }
}
