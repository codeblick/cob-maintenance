<?php

declare(strict_types=1);

namespace CobMaintenance\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\Framework\Adapter\Cache\CacheClearer;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class MaintenanceController extends StorefrontController
{
    private Connection $connection;

    private CacheClearer $cacheClearer;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection, CacheClearer $cacheClearer)
    {
        $this->connection = $connection;
        $this->cacheClearer = $cacheClearer;
    }

    /**
     * @Route("/maintenance/login", name="frontend.maintenance.login", methods={"POST"}, defaults={"XmlHttpRequest"=true, "csrf_protected"=false})
     *
     * @param Request $request
     * @param SalesChannelContext $context
     * @return Response
     */
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

    /**
     * @param Request $request
     * @return boolean
     */
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

    /**
     * @param Request $request
     * @param SalesChannelContext $context
     * @return bool
     */
    private function addIpToAllowedList(Request $request, SalesChannelContext $context): bool
    {
        return $this->connection->executeStatement(
            'UPDATE sales_channel
             SET maintenance_ip_whitelist = JSON_ARRAY_APPEND(maintenance_ip_whitelist, "$", :ipAddress)
             WHERE id = UNHEX(:salesChannelId)',
            [
                'ipAddress' => (string) $request->getClientIp(),
                'salesChannelId' => $context->getSalesChannelId()
            ]
        ) == 1;
    }
}
