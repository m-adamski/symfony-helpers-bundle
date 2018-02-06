<?php

namespace Adamski\Symfony\HelpersBundle\Helper;

use Adamski\Symfony\HelpersBundle\Model\PaginableRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class PaginationHelper {

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * PaginatorHelper constructor.
     *
     * @param SessionInterface $session
     * @param RouterInterface  $router
     */
    public function __construct(SessionInterface $session, RouterInterface $router) {
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * Generate response for Controller.
     *
     * @param Request             $request
     * @param PaginableRepository $repository
     * @param int                 $page
     * @param int                 $limit
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function responseData(Request $request, PaginableRepository $repository, int $page = 1, int $limit = 20) {

        // Get Doctrine Paginator object
        $paginator = $repository->getPaginated($page, $limit);

        // Get variables from Request
        $currentRoute = $request->get("_route");
        $routeParams = $request->get("_route_params");

        // Store current page into Session
        $this->session->set("_paginator", [
            "_route"        => $currentRoute,
            "_route_params" => $routeParams
        ]);

        return $paginator;
    }

    /**
     * Check Paginator parameters stored in Session and generate RedirectResponse based on specified route.
     *
     * @param string $route
     * @param array  $parameters
     * @return RedirectResponse
     */
    public function redirect(string $route, array $parameters = []) {

        // Check if Session has stored parameters for specified route
        if ($paginatorParams = $this->session->get("_paginator")) {
            if (is_array($paginatorParams) && array_key_exists("_route", $paginatorParams) && array_key_exists("_route_params", $paginatorParams)) {
                if ($paginatorParams["_route"] == $route) {

                    // Get Route parameters stored in Session
                    $routeParams = $paginatorParams["_route_params"];

                    // Define final parameters array
                    $finalParams = [];

                    if (is_array($routeParams) && array_key_exists("page", $routeParams)) {
                        $finalParams = array_merge_recursive($finalParams, ["page" => $routeParams["page"]]);
                        unset($routeParams["page"]);
                    }

                    $finalParams = array_merge_recursive($finalParams, $routeParams, $parameters);

                    return new RedirectResponse(
                        $this->router->generate($route, $finalParams)
                    );
                }
            }
        }

        return new RedirectResponse(
            $this->router->generate($route, $parameters)
        );
    }
}
