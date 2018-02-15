<?php

namespace Adamski\Symfony\HelpersBundle\Helper;

use Adamski\Symfony\HelpersBundle\Model\Notification;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class NotificationHelper {

    const SESSION_NAMESPACE = "NotificationsBag";

    const ALERT_TYPE = "alert";
    const SUCCESS_TYPE = "success";
    const WARNING_TYPE = "warning";
    const ERROR_TYPE = "error";
    const INFO_TYPE = "info";
    const INFORMATION_TYPE = "information";

    /**
     * @var FlashBagInterface
     */
    protected $flashSession;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $allowedTypes = ["alert", "success", "warning", "error", "info", "information"];

    /**
     * NotificationHelper constructor.
     *
     * @param FlashBagInterface $flashSession
     * @param RouterInterface   $router
     */
    public function __construct(FlashBagInterface $flashSession, RouterInterface $router) {
        $this->flashSession = $flashSession;
        $this->router = $router;
    }

    /**
     * Add Notification with specified type and text.
     *
     * @param string $type
     * @param string $text
     */
    public function addNotification(string $type, string $text) {

        // Check if specified notification type is allowed
        if (!in_array($type, $this->allowedTypes)) {
            throw new InvalidArgumentException("Specified notification type is not allowed");
        }

        // Get stored Notifications
        $storedNotifications = $this->flashSession->get(self::SESSION_NAMESPACE);

        // Add new Notification
        $storedNotifications[] = new Notification($type, $text);

        // Store Notifications in Session
        $this->flashSession->set(self::SESSION_NAMESPACE, $storedNotifications);
    }

    /**
     * Add Notification and redirect to specified Url.
     *
     * @param string $url
     * @param string $type
     * @param string $text
     * @return RedirectResponse
     */
    public function redirectNotification(string $url, string $type, string $text) {
        $this->addNotification($type, $text);

        return new RedirectResponse($url);
    }

    /**
     * Add Notification and redirect to specified Route.
     *
     * @param string $route
     * @param string $type
     * @param string $text
     * @param array  $routeParams
     * @return RedirectResponse
     */
    public function routeRedirectNotification(string $route, string $type, string $text, array $routeParams = []) {
        return $this->redirectNotification(
            $this->router->generate($route, $routeParams), $type, $text
        );
    }

    /**
     * Clear Notifications collection.
     */
    public function clear() {
        $this->flashSession->set(self::SESSION_NAMESPACE, []);
    }

    /**
     * Get Notifications collection.
     *
     * @return array
     */
    public function getNotifications() {
        return $this->flashSession->get(self::SESSION_NAMESPACE);
    }
}
