<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Connector\Reader\File\Xlsx;

use Akeneo\Component\Batch\Job\JobParameters;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Component\Connector\ArrayConverter\ArrayConverterInterface;
use Pim\Component\Connector\Exception\DataArrayConversionException;
use Pim\Component\Connector\Reader\File\FileIteratorFactory;
use Pim\Component\Connector\Reader\File\Xlsx\Reader;

/**
 * Class GoogleCategoryReader
 *
 * @author                 Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class GoogleCategoryReader extends Reader
{

    /** @staticvar AKENEO_CATEGORY_CODE */
    const AKENEO_CATEGORY_CODE = 'code';

    /** @staticvar AKENEO_CATEGORY_LABEL */
    const AKENEO_CATEGORY_LABEL = 'label-';

    /** @staticvar AKENEO_CATEGORY_PARENT */
    const AKENEO_CATEGORY_PARENT = 'parent';

    /** @var LocaleRepositoryInterface LocaleRepositoryInterface */
    protected $localeRepositoryInterface;

    /**
     * GoogleCategoryReader constructor.
     *
     * @param FileIteratorFactory $fileIteratorFactory
     * @param ArrayConverterInterface $converter
     * @param array $options
     * @param LocaleRepositoryInterface $localeRepositoryInterface
     */
    public function __construct(
        FileIteratorFactory $fileIteratorFactory,
        ArrayConverterInterface $converter,
        array $options = [],
        LocaleRepositoryInterface $localeRepositoryInterface
    ) {
        parent::__construct($fileIteratorFactory, $converter, $options);
        $this->localeRepositoryInterface = $localeRepositoryInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        /** @var JobParameters $jobParameters */
        $jobParameters = $this->stepExecution->getJobParameters();
        /** @var string $filePath */
        $filePath = $jobParameters->get('filePath');
        if (null === $this->fileIterator) {
            $this->fileIterator = $this->fileIteratorFactory->create($filePath, $this->options);
            $this->fileIterator->rewind();
        }

        $this->fileIterator->next();

        if ($this->fileIterator->valid() && null !== $this->stepExecution) {
            $this->stepExecution->incrementSummaryInfo('item_position');
        }
        /** @var mixed $data */
        $data = $this->fileIterator->current();

        if (null === $data) {
            return null;
        }
        /** @var mixed $data */
        $data = $this->formatGoogleCategories($data, $jobParameters);
        /** @var array $headers */
        $headers = [
            0 => self::AKENEO_CATEGORY_CODE,
            1 => self::AKENEO_CATEGORY_LABEL.$this->getLocaleFromFile($filePath),
            2 => self::AKENEO_CATEGORY_PARENT,
        ];
        /** @var int $countHeaders */
        $countHeaders = count($headers);
        /** @var int $countData */
        $countData = count($data);

        $this->checkColumnNumber($countHeaders, $countData, $data, $filePath);

        if ($countHeaders > $countData) {
            /** @var int $missingValuesCount */
            $missingValuesCount = $countHeaders - $countData;
            /** @var array $missingValues */
            $missingValues = array_fill(0, $missingValuesCount, '');
            $data          = array_merge($data, $missingValues);
        }
        /** @var array $item */
        $item = array_combine($headers, $data);

        try {
            $item = $this->converter->convert($item, $this->getArrayConverterOptions());
        } catch (DataArrayConversionException $e) {
            $this->skipItemFromConversionException($item, $e);
        }

        return $item;
    }

    /**
     * @param array $data
     * @param JobParameters $jobParameters
     *
     * @return array $googleCategories
     * @throws \Exception
     */
    protected function formatGoogleCategories(array $data, JobParameters $jobParameters): array
    {
        /** @var string $googleCategorySuffix */
        $googleCategorySuffix = $jobParameters->get('suffix_category');
        /** @var array $googleCategories */
        $googleCategories = [];
        if (!isset($data[0])) {
            throw new \Exception('Invalid offset');
        }
        $googleCategories[0] = $googleCategorySuffix.$data[0];
        unset($data[0]);
        $data = array_filter($data);
        $data = array_values(array_slice($data, -1));
        if (!isset($data[0])) {
            throw new \Exception('Invalid offset');
        }
        $googleCategories[1] = $data[0];
        $googleCategories[2] = $jobParameters->get('master_category');

        return $googleCategories;
    }

    /**
     * @param string $filePath
     *
     * @return string $pimLocale
     * @throws \Exception
     */
    public function getLocaleFromFile(string $filePath): string
    {
        /** @var array $fileName */
        $fileName = pathinfo($filePath);
        if (!isset($fileName['filename'])) {
            throw new \Exception('Invalid offset');
        }
        /** @var string $fileLocale */
        $fileLocale = substr($fileName['filename'], -5);
        /** @var string $pimLocale */
        $pimLocale = str_replace('-', '_', $fileLocale);

        if (!in_array($pimLocale, $this->localeRepositoryInterface->getActivatedLocaleCodes())) {
            throw new \Exception('No actived locale with this code');
        }

        return $pimLocale;
    }
}
