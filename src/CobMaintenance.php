<?php

declare(strict_types=1);

namespace CobMaintenance;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CobMaintenance extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function activate(ActivateContext $activateContext): void
    {
        $connection = $this->container->get(Connection::class);

        $connection->executeStatement(
            'UPDATE system_config
            SET configuration_value = JSON_SET(configuration_value, "$._value", true)
            WHERE configuration_key = "core.mailerSettings.disableDelivery";'
        );
    }
}
