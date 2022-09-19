<?php

namespace BrizyEkklesia\Placeholder\Ekklesia360;

use AppBundle\Services\MinistryBrands\MonkCMS\MonkCMSService;
use BrizyPlaceholders\AbstractPlaceholder;
use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;
use Exception;
use Twig_Environment;

abstract class PlaceholderAbstract extends AbstractPlaceholder
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var MonkCMSService
     */
    protected $monkCMS;

    public function __construct($twig, MonkCMSService $monkCMS)
    {
        $this->twig    = $twig;
        $this->monkCMS = $monkCMS;
    }

    /**
     * @param $placeholderName
     * @return bool
     */
    public function support($placeholderName)
    {
        return $placeholderName == $this->name;
    }

    /**
     * @param ContextInterface $context
     * @param ContentPlaceholder $placeholder
     * @return string
     * @throws Exception
     */
    public function getValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        ob_start(); ob_clean();
            $this->echoValue($context, $placeholder);
        return ob_get_clean();
    }

    /**
     * @param ContextInterface $context
     * @param ContentPlaceholder $placeholder
     * @return void
     * @throws Exception
     */
    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder) {
        throw new Exception('Rewrite it in the child class: ' . get_called_class());
    }
}