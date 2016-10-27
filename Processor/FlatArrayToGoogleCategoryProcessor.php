<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Processor;

use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Item\InvalidItemException;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Dnd\Bundle\GoogleShoppingConnectorBundle\Entity\GoogleCategory;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;

/**
 * This processor is used to convert array coming from the CSV google category reader
 * into a GoogleCategory entity. The resulting GoogleCategory will be sent to the Doctrine
 * Writer in order to be persisted in the database.
 *
 * @author Florian Fauvel <florian.fauvel@dnd.fr>
 * @copyright 2016 Agence Dn'D (http://www.dnd.fr)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FlatArrayToGoogleCategoryProcessor extends AbstractConfigurableStepElement implements
    ItemProcessorInterface,
    StepExecutionAwareInterface
{
    /** @const string */
    const GOOGLE_CATEGORY_CODE = 'code';

    /** @const string */
    const GOOGLE_CATEGORY_NAME = 'name';

    /** @var  StepExecution */
    protected $stepExecution;

    /** @var  ObjectRepository */
    protected $googleCategoryRepository;

    /** @var LocaleRepositoryInterface */
    protected $localeRepository;

    /** @var string */
    protected $locale;

    /**
     * @param ObjectRepository          $googleCategoryRepository
     * @param LocaleRepositoryInterface $localeRepository
     */
    public function __construct(
        ObjectRepository $googleCategoryRepository,
        LocaleRepositoryInterface $localeRepository
    ) {
        $this->googleCategoryRepository = $googleCategoryRepository;
        $this->localeRepository         = $localeRepository;
    }

    /**
     * Set locale
     *
     * @param string $localeCode Locale code
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string Locale code
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get locale codes for select option
     *
     * @return array
     */
    protected function getLocaleCodes()
    {
        $choices = [];
        foreach ($this->localeRepository->getActivatedLocales() as $locale) {
            $choices[$locale->getCode()] = $locale->getCode();
        }
        return $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFields()
    {
        return [
            'locale' => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => $this->getLocaleCodes(),
                    'required' => true,
                    'select2'  => true,
                    'label'    => 'dnd_google_shopping_connector.export.locale.label',
                    'help'     => 'dnd_google_shopping_connector.export.locale.help'
                ]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        $item = $this->formatColumns($item);
        
        $this->checkColumns($item);

        return $this->denormalize($item);
    }

    /**
     * Format Item to get the required columns
     *
     * @param array $item
     *
     * @return array $newItem
     */
    public function formatColumns($item)
    {
        $newItem                               = [];
        $newItem[static::GOOGLE_CATEGORY_CODE] = $item[0];
        unset($item[0]);
        $item                                  = array_filter($item);
        $newItem[static::GOOGLE_CATEGORY_NAME] = implode(' > ', $item);
        return $newItem;
    }

    /**
     * Checks that Item contains required columns.
     *
     * @param array $item
     *
     * @throws InvalidItemException
     */
    public function checkColumns(array $item)
    {
        if (!isset($item[self::GOOGLE_CATEGORY_CODE])) {
            $this->setItemError($item, 'job_execution.summary.missing_code');
        }

        if (!isset($item[self::GOOGLE_CATEGORY_NAME])) {
            $this->setItemError($item, 'job_execution.summary.missing_name');
        }
    }

    /**
     * Denormalizes a flat array into a GoogleCategory entity
     *
     * @param array $item
     *
     * @return GoogleCategory
     */
    public function denormalize(array $item)
    {

        $googleCategory = $this->googleCategoryRepository->findOneByCode($item[static::GOOGLE_CATEGORY_CODE]);

        if (null === $googleCategory) {
            $googleCategory = new GoogleCategory();
            $googleCategory->setCode($item[static::GOOGLE_CATEGORY_CODE]);
        }

        $googleCategory->setLocale($this->getLocale())->setName($item[static::GOOGLE_CATEGORY_NAME]);

        return $googleCategory;
    }

    /**
     * @param array $item
     * @param       $error
     *
     * @throws InvalidItemException
     */
    protected function setItemError(array $item, $error)
    {
        if ($this->stepExecution) {
            $this->stepExecution->incrementSummaryInfo('skip');
        }

        throw new InvalidItemException($error, $item);
    }

    /**
     * @param StepExecution $stepExecution
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }
}
