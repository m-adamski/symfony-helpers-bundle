<?php

namespace Adamski\Symfony\HelpersBundle\Twig;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PaginationExtension extends AbstractExtension {

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return [
            new TwigFunction("pagination", [$this, "renderPagination"], ["is_safe" => ["html"], "needs_environment" => true])
        ];
    }

    /**
     * Render Paginator template.
     *
     * @param Environment $environment
     * @param Paginator   $paginator
     * @param string      $route
     * @param int         $page
     * @param int         $limit
     * @return bool|string
     */
    public function renderPagination(Environment $environment, Paginator $paginator, string $route, int $page = 1, int $limit = 20) {
        try {
            return $environment->render("pagination.html.twig", [
                "current_page" => $page,
                "max_pages"    => ceil($paginator->count() / $limit),
                "route"        => $route
            ]);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
