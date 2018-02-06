<?php

namespace Adamski\Symfony\HelpersBundle\Helper;

use Adamski\Symfony\HelpersBundle\Model\Notification;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class NotificationHelper {

    const SESSION_NAMESPACE = "NotificationsBag";

    /**
     * @var FlashBagInterface
     */
    protected $flashSession;

    /**
     * @var array
     */
    protected $allowedTypes = ["alert", "success", "warning", "error", "info", "information"];

    /**
     * NotificationHelper constructor.
     *
     * @param FlashBagInterface $flashSession
     */
    public function __construct(FlashBagInterface $flashSession) {
        $this->flashSession = $flashSession;
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
