<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Reader\File;


use Pim\Component\Connector\Reader\File\CsvReader;
use Akeneo\Component\Batch\Item\InvalidItemException;
use Symfony\Component\Validator\Constraints as Assert;
use Akeneo\Component\Classification\Repository\CategoryRepositoryInterface;

/**
 * Class GoogleShoppingCategoryCsvReader
 *
 * @package   Dnd\Bundle\GoogleShoppingConnectorBundle\Reader\File
 * @author    Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright Agence Dn'D <http://www.dnd.fr>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GoogleShoppingCategoryCsvReader extends CsvReader
{

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var
     */
    protected $parent;

    /**
     * @param $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * GoogleShoppingCategoryCsvReader constructor.
     *
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (null === $this->csv) {
            $this->initializeRead();
        }

        $data = $this->csv->fgetcsv();

        if (false !== $data) {
            if ([null] === $data || null === $data) {
                return null;
            }
            if ($this->stepExecution) {
                $this->stepExecution->incrementSummaryInfo('read_lines');
            }
        } elseif ($this->csv->eof()) {
            $data = null;
        } else {
            throw new \RuntimeException('An error occurred while reading the csv.');
        }

        $data = $this->formatColmuns($data);

        return $data;
    }

    /**
     * Initialize read process by extracting zip if needed, setting CSV options
     * and settings field names.
     */
    protected function initializeRead()
    {
        // TODO mime_content_type is deprecated, use Symfony\Component\HttpFoundation\File\MimeTypeMimeTypeGuesser?
        if ('application/zip' === mime_content_type($this->filePath)) {
            $this->extractZipArchive();
        }

        $this->csv = new \SplFileObject($this->filePath);
        $this->csv->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::SKIP_EMPTY
        );
        $this->csv->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
    }


    /**
     * @return mixed
     */
    public function getLocaleFromCsv()
    {
        $filePath = $this->getFilePath();
        $fileName = pathinfo($filePath);
        $fileLocale = substr($fileName['filename'], -5);

        return $pimLocale = str_replace('-', '_', $fileLocale);

    }

    /**
     * @param $data
     *
     * @return array
     */
    public function formatColmuns($data)
    {

        $newData = [];
        $newData['code'] = 'google_category_'.$data[0];
        $newData['googleCategoryId'] = $data[0];
        unset($data[0]);
        $newData['parent'] = $this->getParent();
        $data = array_filter($data);
        $newData['label-'.$this->getLocaleFromCsv()] = array_values(array_slice($data, -1))[0];

        return $newData;
    }

    /**
     * @return array
     */
    protected function getCategoriesChoices()
    {
        $choices = [];
        foreach ($this->categoryRepository->getOrderedAndSortedByTreeCategories() as $category) {
            $choices[$category->getCode()] = $category->getCode();
        }

        return $choices;
    }

    /**
     * @return mixed
     */
    public function getConfigurationFields()
    {
        return
            array_merge(
                parent::getConfigurationFields(),
                [
                    'parent' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getCategoriesChoices(),
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.import.category.label',
                            'help'     => 'dnd_google_shopping_connector.import.category.help',
                            'required' => false,
                        ],
                    ],
                ]
            );
    }
}
