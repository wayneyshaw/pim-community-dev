<?php

namespace Pim\Component\Catalog\Manager;

use Pim\Component\Catalog\Model\ProductInterface;
use Pim\Component\Catalog\Model\ProductTemplateInterface;

/**
 * Product template manager
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ProductTemplateApplierInterface
{
    /**
     * @param ProductTemplateInterface $template
     * @param ProductInterface[]       $products
     *
     * @return array $violations
     */
    public function apply(ProductTemplateInterface $template, array $products);
}
