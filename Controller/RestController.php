<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Controller;

use Akeneo\Component\Classification\Repository\CategoryRepositoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Pim\Component\Catalog\Repository\CurrencyRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class RestController
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class RestController
{

    /** @var CategoryRepositoryInterface $categoryRepository */
    protected $categoryRepository;

    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;

    /** @var CurrencyRepositoryInterface $currencyRepository */
    protected $currencyRepository;


    /**
     * RestController constructor.
     *
     * @param CategoryRepositoryInterface  $categoryRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param CurrencyRepositoryInterface  $currencyRepository
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        AttributeRepositoryInterface $attributeRepository,
        CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->attributeRepository = $attributeRepository;
        $this->currencyRepository = $currencyRepository;

    }

    /**
     * @return JsonResponse
     */
    public function listParentCategoriesAction()
    {
        /** @var array $parentCategories */
        $parentCategories = [];
        foreach ($this->categoryRepository->getOrderedAndSortedByTreeCategories() as $parentCategory) {
            $parentCategories[$parentCategory->getCode()] = $parentCategory->getCode();
        }

        return new JsonResponse(array_combine($parentCategories, $parentCategories));
    }

    /**
     * @return JsonResponse
     */
    public function listAttributesAction()
    {
        /** @var array $attributesList */
        $attributesList = [];
        $attributesList[''] = '';
        foreach ($this->attributeRepository->getAttributesAsArray() as $attribute) {
            $attributesList[$attribute['code']] = $attribute['code'];
        }

        return new JsonResponse(array_combine($attributesList, $attributesList));
    }


    /**
     * @return JsonResponse
     */
    public function listAvailableCurrenciesAction()
    {
        /** @var array $availableCurrenciesList */
        $availableCurrenciesList = [];
        $availableCurrenciesList[''] = '';
        foreach ($this->currencyRepository->getActivatedCurrencies() as $currency) {
            $availableCurrenciesList[$currency->getCode()] = $currency->getCode();
        }

        return new JsonResponse(array_combine($availableCurrenciesList, $availableCurrenciesList));
    }
}
