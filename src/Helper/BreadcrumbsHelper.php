<?php

namespace Adamski\Symfony\HelpersBundle\Helper;

use Adamski\Symfony\HelpersBundle\Model\Breadcrumb;
use InvalidArgumentException;
use Symfony\Component\Routing\RouterInterface;

class BreadcrumbsHelper {

    const DEFAULT_NAMESPACE = "default";

    /**
     * @var array
     */
    protected $breadcrumbs = [
        self::DEFAULT_NAMESPACE => []
    ];

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * BreadcrumbsHelper constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router) {
        $this->router = $router;
    }

    /**
     * Add Breadcrumb item at the end of default namespace collection.
     *
     * @param string $text
     * @param string $url
     * @param string $translationDomain
     * @param array  $translationParameters
     * @param bool   $translate
     * @return BreadcrumbsHelper
     */
    public function addItem(string $text, string $url = "", string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true) {
        return $this->addNamespaceItem(self::DEFAULT_NAMESPACE, $text, $url, $translationDomain, $translationParameters, $translate);
    }

    /**
     * Add Breadcrumb item at the end of default namespace collection.
     *
     * @param string $text
     * @param string $route
     * @param array  $routeParameters
     * @param string $translationDomain
     * @param array  $translationParameters
     * @param bool   $translate
     * @return BreadcrumbsHelper
     */
    public function addRouteItem(string $text, string $route, array $routeParameters = [], string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true) {
        return $this->addNamespaceItem(self::DEFAULT_NAMESPACE, $text, $this->router->generate($route, $routeParameters), $translationDomain, $translationParameters, $translate);
    }

    /**
     * Add Breadcrumb item at the end of specified namespace collection.
     *
     * @param string $namespace
     * @param string $text
     * @param string $url
     * @param string $translationDomain
     * @param array  $translationParameters
     * @param bool   $translate
     * @return $this
     */
    public function addNamespaceItem(string $namespace, string $text, string $url = "", string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true) {
        $b = new Breadcrumb($text, $url, $translationDomain, $translationParameters, $translate);
        $this->breadcrumbs[$namespace][] = $b;

        return $this;
    }

    /**
     * Add Breadcrumb item at the beginning of default namespace collection.
     *
     * @param string $text
     * @param string $url
     * @param string $translationDomain
     * @param array  $translationParameters
     * @param bool   $translate
     * @return BreadcrumbsHelper
     */
    public function prependItem(string $text, string $url = "", string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true) {
        return $this->prependNamespaceItem(self::DEFAULT_NAMESPACE, $text, $url, $translationDomain, $translationParameters, $translate);
    }

    /**
     * Add Breadcrumb item at the beginning of default namespace collection.
     *
     * @param string $text
     * @param string $route
     * @param array  $routeParameters
     * @param string $translationDomain
     * @param array  $translationParameters
     * @param bool   $translate
     * @return BreadcrumbsHelper
     */
    public function prependRouteItem(string $text, string $route, array $routeParameters = [], string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true) {
        return $this->prependNamespaceItem(self::DEFAULT_NAMESPACE, $text, $this->router->generate($route, $routeParameters), $translationDomain, $translationParameters, $translate);
    }

    /**
     * Add Breadcrumb item at the beginning of specified namespace collection.
     *
     * @param string $namespace
     * @param string $text
     * @param string $url
     * @param string $translationDomain
     * @param array  $translationParameters
     * @param bool   $translate
     * @return $this
     */
    public function prependNamespaceItem(string $namespace, string $text, string $url = "", string $translationDomain = "breadcrumbs", array $translationParameters = [], bool $translate = true) {
        $b = new Breadcrumb($text, $url, $translationDomain, $translationParameters, $translate);
        array_unshift($this->breadcrumbs[$namespace], $b);

        return $this;
    }

    /**
     * Clear specified or all namespaces collections.
     *
     * @param string $namespace
     * @return $this
     */
    public function clear(string $namespace = "") {
        if (!empty($namespace)) {
            $this->breadcrumbs[$namespace] = [];
        } else {
            $this->breadcrumbs = [
                self::DEFAULT_NAMESPACE => []
            ];
        }

        return $this;
    }

    /**
     * Return Breadcrumbs from specified namespace.
     *
     * @param string $namespace
     * @return array
     */
    public function getNamespaceBreadcrumbs(string $namespace = self::DEFAULT_NAMESPACE) {

        // Check whether requested namespace breadcrumbs is exists
        if (!array_key_exists($namespace, $this->breadcrumbs)) {
            throw new InvalidArgumentException(sprintf(
                'The breadcrumb namespace "%s" does not exist', $namespace
            ));
        }

        return $this->breadcrumbs[$namespace];
    }
}
