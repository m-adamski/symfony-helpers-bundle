<?php

namespace Adamski\Symfony\HelpersBundle\Twig;

use Adamski\Symfony\HelpersBundle\Helper\BreadcrumbsHelper;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class BreadcrumbsExtension extends AbstractExtension {

    /**
     * @var BreadcrumbsHelper
     */
    protected $breadcrumbsHelper;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * BreadcrumbsExtension constructor.
     *
     * @param BreadcrumbsHelper   $breadcrumbsHelper
     * @param TranslatorInterface $translator
     */
    public function __construct(BreadcrumbsHelper $breadcrumbsHelper, TranslatorInterface $translator) {
        $this->breadcrumbsHelper = $breadcrumbsHelper;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return [
            new TwigFunction("breadcrumbs", [$this, "renderBreadcrumbs"], ["is_safe" => ["html"], "needs_environment" => true])
        ];
    }

    /**
     * Render Breadcrumbs template.
     *
     * @param Environment $environment
     * @param string      $namespace
     * @return bool|string
     */
    public function renderBreadcrumbs(Environment $environment, string $namespace = "") {

        $breadcrumbs = empty($namespace) ? $this->breadcrumbsHelper->getNamespaceBreadcrumbs() : $this->breadcrumbsHelper->getNamespaceBreadcrumbs($namespace);

        try {
            return $environment->render("@Helpers/breadcrumbs.html.twig", ["breadcrumbs" => $breadcrumbs]);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
