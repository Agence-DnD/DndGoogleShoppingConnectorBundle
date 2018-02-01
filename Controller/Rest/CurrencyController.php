<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Controller\Rest;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CurrencyController
 *
 * @author                 Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class CurrencyController extends \Pim\Bundle\EnrichBundle\Controller\Rest\CurrencyController
{

    /**
     * @return JsonResponse
     */
    public function listAllAction()
    {
        /** @var array $availableCurrenciesList */
        $availableCurrenciesList     = [];
        $availableCurrenciesList[''] = '';
        foreach ($this->currencyRepository->getActivatedCurrencies() as $currency) {
            $availableCurrenciesList[$currency->getCode()] = $currency->getCode();
        }

        return new JsonResponse(array_combine($availableCurrenciesList, $availableCurrenciesList));
    }
}
