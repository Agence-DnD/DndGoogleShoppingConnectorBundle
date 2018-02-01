<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AttributeController
 *
 * @author                 Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class AttributeController extends \Pim\Bundle\EnrichBundle\Controller\Rest\AttributeController
{

    /**
     * @return JsonResponse
     */
    public function listAllAction()
    {
        /** @var array $attributes */
        $attributes = [];
        foreach ($this->attributeRepository->getAttributesAsArray() as $attribute) {
            if (!isset($attribute['code'])) {
                continue;
            }
            $attributes[$attribute['code']] = $attribute['code'];
        }

        return new JsonResponse(array_combine($attributes, $attributes));
    }
}
