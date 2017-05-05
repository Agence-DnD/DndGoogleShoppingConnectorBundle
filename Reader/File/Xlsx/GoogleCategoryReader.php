<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Reader\File\Xlsx;

use Pim\Component\Connector\Reader\File\Xlsx\Reader;
use Pim\Component\Connector\Exception\DataArrayConversionException;

/**
 * Class GoogleCategoryReader
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class GoogleCategoryReader extends Reader
{

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        /** @var array $jobParameters */
        $jobParameters = $this->stepExecution->getJobParameters();
        /** @var string $filePath */
        $filePath = $jobParameters->get('filePath');
        /** @var string $parentCategories */
        $parentCategories = $jobParameters->get('parentCategories');

        if (null === $this->fileIterator) {
            $this->fileIterator = $this->fileIteratorFactory->create($filePath, $this->options);
            $this->fileIterator->rewind();
        }

        $this->fileIterator->next();

        if ($this->fileIterator->valid() && null !== $this->stepExecution) {
            $this->stepExecution->incrementSummaryInfo('item_position');
        }

        $data = $this->fileIterator->current();

        if (null === $data) {
            return null;
        }
        $data = $this->formatData($data, $parentCategories);
        /** @var array $headers */
        $headers = [0 => 'code', 1 => 'label-'.$this->getLocaleFromCsv($filePath), '2' => 'parent'];

        $countHeaders = count($headers);
        $countData = count($data);

        $this->checkColumnNumber($countHeaders, $countData, $data, $filePath);

        if ($countHeaders > $countData) {
            $missingValuesCount = $countHeaders - $countData;
            $missingValues = array_fill(0, $missingValuesCount, '');
            $data = array_merge($data, $missingValues);
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
     * @param $data
     * @param $parentCategories
     *
     * @return array
     */
    public function formatData($data, $parentCategories)
    {
        /** @var array $newData */
        $newData = [];
        $newData[0] = 'google_category_'.$data[0];
        unset($data[0]);
        $data = array_filter($data);
        $newData[1] = array_values(array_slice($data, -1))[0];
        $newData[2] = $parentCategories;

        return $newData;
    }

    /**
     * @return mixed
     */
    public function getLocaleFromCsv($filePath)
    {
        /** @var array $fileName */
        $fileName = pathinfo($filePath);
        /** @var string $fileLocale */
        $fileLocale = substr($fileName['filename'], -5);

        return $pimLocale = str_replace('-', '_', $fileLocale);
    }
}
