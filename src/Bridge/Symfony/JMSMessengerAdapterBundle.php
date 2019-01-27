<?php

declare(strict_types=1);

namespace KunicMarko\JMSMessengerAdapter\Bridge\Symfony;

use KunicMarko\JMSMessengerAdapter\Bridge\Symfony\DependencyInjection\JMSMessengerAdapterExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class JMSMessengerAdapterBundle extends Bundle
{
    public function getContainerExtension(): JMSMessengerAdapterExtension
    {
        return new JMSMessengerAdapterExtension();
    }
}
