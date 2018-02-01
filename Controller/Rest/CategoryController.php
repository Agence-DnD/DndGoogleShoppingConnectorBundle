<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CategoryController
 *
 * @author                 Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class CategoryController extends \Pim\Bundle\EnrichBundle\Controller\Rest\CategoryController
{

    /**
     * @return JsonResponse
     */
    public function listAllAction()
    {
        /** @var array $categories */
        $categories = [];
        foreach ($this->repository->getOrderedAndSortedByTreeCategories() as $category) {
            $categories[$category->getCode()] = $category->getCode();
        }

        return new JsonResponse(array_combine($categories, $categories));
    }
}
