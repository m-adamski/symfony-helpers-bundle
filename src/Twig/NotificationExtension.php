<?php

namespace App\Twig;

use Adamski\Symfony\HelpersBundle\Helper\NotificationHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension {

    /**
     * @var NotificationHelper
     */
    protected $notificationHelper;

    /**
     * NotificationExtension constructor.
     *
     * @param NotificationHelper $notificationHelper
     */
    public function __construct(NotificationHelper $notificationHelper) {
        $this->notificationHelper = $notificationHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return [
            new TwigFunction("notification", [$this, "renderNotification"], ["is_safe" => ["html"], "needs_environment" => true])
        ];
    }

    /**
     * Render Notifications template.
     *
     * @param Environment $environment
     * @return bool|string
     */
    public function renderNotification(Environment $environment) {

        // Get stored Notifications
        $storedNotifications = $this->notificationHelper->getNotifications();

        if (count($storedNotifications) > 0) {
            try {
                return $environment->render("notification.html.twig", ["notifications" => $storedNotifications]);
            } catch (\Exception $exception) {
                return false;
            }
        }

        return false;
    }
}
