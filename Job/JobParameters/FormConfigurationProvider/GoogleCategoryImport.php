<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Job\JobParameters\FormConfigurationProvider;


use Pim\Bundle\ImportExportBundle\JobParameters\FormConfigurationProviderInterface;
use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Classification\Repository\CategoryRepositoryInterface;

/**
 * Class GoogleCategoryImport
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class GoogleCategoryImport implements FormConfigurationProviderInterface
{

    /** @var FormConfigurationProviderInterface $simpleProvider */
    protected $simpleProvider;

    /**
     * @var array $supportedJobNames
     */
    protected $supportedJobNames;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;


    /**
     * GoogleCategoryImport constructor.
     *
     * @param FormConfigurationProviderInterface $simpleProvider
     * @param CategoryRepositoryInterface        $categoryRepository
     * @param array                              $supportedJobNames
     */
    public function __construct(
        FormConfigurationProviderInterface $simpleProvider,
        CategoryRepositoryInterface $categoryRepository,
        array $supportedJobNames
    ) {
        $this->simpleProvider = $simpleProvider;
        $this->categoryRepository = $categoryRepository;
        $this->supportedJobNames = $supportedJobNames;
    }


    /**
     * @return array
     */
    public function getFormConfiguration()
    {
        $csvFormOptions = $this->simpleProvider->getFormConfiguration();

        $customOptions = [
            'parentCategories' => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => $this->getCategoriesCode(),
                    'required' => true,
                    'select2'  => true,
                    'label'    => 'google_shopping.import.csv.locale.label',
                    'help'     => 'google_shopping.import.csv.locale.help',
                ],
            ],
        ];

        return array_merge($customOptions, $csvFormOptions);
    }

    /**
     * @param JobInterface $job
     *
     * @return bool
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }


    protected function getCategoriesCode()
    {
        $categoriesCode = [];
        $categories = $this->categoryRepository->getOrderedAndSortedByTreeCategories();
        foreach ($categories as $category){
            $categoriesCode[$category->getCode()] = $category->getCode();
        }
        return $categoriesCode;
    }

}
