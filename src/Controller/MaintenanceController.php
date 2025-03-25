<?php

declare(strict_types=1);

namespace CobMaintenance\Controller;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class MaintenanceController extends StorefrontController
{
    private Connection $connection;

    private CacheClearer $cacheClearer;

    /**
     * @var EntityRepository
     */
    private $salesChannelRepository;

    public function __construct(
        Connection $connection,
        CacheClearer $cacheClearer,
        EntityRepository $salesChannelRepository
    )
    {
        $this->connection = $connection;
        $this->cacheClearer = $cacheClearer;
        $this->salesChannelRepository = $salesChannelRepository;
    }

    #[Route(path: '/maintenance/login', name: 'frontend.maintenance.login', methods: ['POST'], defaults: ['XmlHttpRequest' => true, 'csrf_protected' => false])]
    public function maintenanceLogin(Request $request, SalesChannelContext $context): Response
    {
        if (!$this->verifyUser($request)) {
            return new JsonResponse(['success' => false], 200);
        };

        $success = $this->addIpToAllowedList($request, $context);

        if ($success) {
            $this->cacheClearer->clear();
        }

        return new JsonResponse(['success' => $success], 200);
    }

    private function verifyUser(Request $request): bool
    {
        if (is_null($request->get('username')) || is_null($request->get('password'))) {
            return false;
        }

        $password = $this->connection->fetchOne(
            'SELECT `password` FROM user WHERE username = :username',
            [
                'username' => $request->get('username')
            ]
        );

        if ($password === false || !password_verify($request->get('password'), $password)) {
            return false;
        }

        return true;
    }

    private function addIpToAllowedList(Request $request, SalesChannelContext $context): bool
    {
        $ipWhitelist = $context->getSalesChannel()->getMaintenanceIpWhitelist();
        $ipWhitelist[] = $request->getClientIp();

        $result = $this->salesChannelRepository->update([
            [
                'id' => $context->getSalesChannelId(),
                'maintenanceIpWhitelist' => $ipWhitelist
            ]
        ], $context->getContext());

        return $result->getErrors() == [];
    }
}
