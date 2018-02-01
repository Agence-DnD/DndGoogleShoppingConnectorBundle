<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Connector\Writer\File\Xml;

use Akeneo\Component\Batch\Job\RuntimeErrorException;
use Akeneo\Component\Buffer\BufferFactory;
use Akeneo\Component\Buffer\BufferInterface;
use Akeneo\Component\FileStorage\Repository\FileInfoRepositoryInterface;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Pim\Component\Catalog\Repository\CurrencyRepositoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Component\Connector\Writer\File\AbstractFileWriter;
use Pim\Component\Connector\Writer\File\ArchivableWriterInterface;

/**
 * Class Writer
 *
 * @author                 Benjamin Hil <benjamin.hil@dnd.fr>
 * @copyright              Copyright (c) 2018 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class Writer extends AbstractFileWriter implements ArchivableWriterInterface
{

    /** @var BufferInterface $buffer */
    protected $buffer;
    /** @var array $writtenFiles */
    protected $writtenFiles = [];
    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;
    /** @var LocaleRepositoryInterface $localeRepository */
    protected $localeRepository;
    /** @var CurrencyRepositoryInterface $currencyRepository */
    protected $currencyRepository;
    /** @var FileInfoRepositoryInterface $fileInfoRepository */
    protected $fileInfoRepository;
    /** @staticvar array GOOGLE_FIELDS */
    const GOOGLE_FIELDS = [
        'g:id'             => 'googleId',
        'g:title'          => 'googleTitle',
        'g:description'    => 'googleDescription',
        'g:link'           => 'googleLink',
        'g:image_link'     => 'googleImagesLink',
        'g:condition'      => 'googleCondition',
        'g:availability'   => 'googleAvailability',
        'g:price'          => 'googlePrice',
        'g:gtin'           => 'googleGtin',
        'g:brand'          => 'googleBrand',
        'g:color'          => 'googleColor',
        'g:gender'         => 'googleGender',
        'g:age_group'      => 'googleAgeGroup',
        'g:material'       => 'googleMaterial',
        'g:size'           => 'googleSize',
        'g:pattern'        => 'googlePattern',
        'g:custom_label_0' => 'googlePersonalizeOne',
        'g:custom_label_1' => 'googlePersonalizeTwo',
        'g:custom_label_2' => 'googlePersonalizeThree',
        'g:custom_label_3' => 'googlePersonalizeFour',
        'g:custom_label_4' => 'googlePersonalizeFive',
    ];

    /**
     * @param BufferFactory $bufferFactory
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LocaleRepositoryInterface $localeRepository
     * @param CurrencyRepositoryInterface $currencyRepository
     * @param FileInfoRepositoryInterface $fileInfoRepository
     */
    public function __construct(
        BufferFactory $bufferFactory,
        AttributeRepositoryInterface $attributeRepository,
        LocaleRepositoryInterface $localeRepository,
        CurrencyRepositoryInterface $currencyRepository,
        FileInfoRepositoryInterface $fileInfoRepository
    ) {
        parent::__construct();

        $this->buffer              = $bufferFactory->create();
        $this->attributeRepository = $attributeRepository;
        $this->localeRepository    = $localeRepository;
        $this->currencyRepository  = $currencyRepository;
        $this->fileInfoRepository  = $fileInfoRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getWrittenFiles()
    {
        return $this->writtenFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $items)
    {
        /** @var array $parameters */
        $parameters = $this->stepExecution->getJobParameters()->all();

        if (false === file_exists($this->getPath())) {
            /** @var \DOMDocument $xml */
            $xml = new \DOMDocument('1.0', 'utf-8');
            /** @var \DOMElement $rss */
            $rss = $xml->createElement('rss');
            $rss->setAttribute('xmlns:g', "http://base.google.com/ns/1.0");
            $rss->setAttribute('version', "1.0");
            /** @var \DOMElement $channel */
            $channel = $xml->createElement('channel');
            if (!isset($parameters['channelTitle'])) {
                return false;
            }
            /** @var \DOMElement $channelTitle */
            $channelTitle = $xml->createElement('title', $parameters['channelTitle']);
            $channel->appendChild($channelTitle);
            if (!isset($parameters['websiteLink'])) {
                return false;
            }
            /** @var \DOMElement $channelLink */
            $channelLink = $xml->createElement('link', $parameters['websiteLink']);
            $channel->appendChild($channelLink);
            if (!isset($parameters['description'])) {
                return false;
            }
            /** @var \DOMElement $channelDescription */
            $channelDescription = $xml->createElement('description', $parameters['description']);
            $channel->appendChild($channelDescription);
            $rss->appendChild($channel);
            $xml->appendChild($rss);
        } else {
            /** @var \DOMDocument $xml */
            $xml = new \DOMDocument('1.0', 'utf-8');
            if (!isset($parameters['filePath'])) {
                return false;
            }
            /** @var mixed $content */
            $content = file_get_contents($parameters['filePath']);
            /** @var string $content */
            $content                 = html_entity_decode($content);
            $xml->formatOutput       = true;
            $xml->preserveWhiteSpace = false;
            $xml->loadXML($content);
            /** @var \DOMNodeList $channel */
            $channel = $xml->getElementsByTagName("channel")->item(0);
        }

        foreach ($items as $product) {
            if (!isset($product['values'])) {
                continue;
            }
            $product['values'] = $this->formatProductArray($product['values']);
            /** @var \DOMElement $item */
            $item = $xml->createElement('item');

            $this->addItemChild(
                'g:google_product_category',
                htmlentities($this->formatGoogleCategory($product, $parameters)),
                $item,
                $xml
            );
            foreach (self::GOOGLE_FIELDS as $googleField => $akeneoAttribute) {
                $this->checkIfItemValueExistAndAddItToXml(
                    $product,
                    $parameters,
                    $item,
                    $xml,
                    $googleField,
                    $akeneoAttribute
                );
            }

            $channel->appendChild($item);
            $xml->formatOutput = true;
        }
        /** @var string $path */
        $path = $this->getPath();
        if (!is_dir(dirname($path))) {
            $this->localFs->mkdir(dirname($path));
        }

        if (false === file_put_contents($path, $xml->saveXML())) {
            throw new RuntimeErrorException('Failed to write to file %path%', ['%path%' => $this->getPath()]);
        }

        return null;
    }

    /**
     * Add new node to xml item node
     *
     * @param string $nodeName
     * @param string $value
     * @param \DomElement $item
     * @param \DomDocument $xml
     *
     * @return \DOMNode|bool
     */
    protected function addItemChild(string $nodeName, string $value, \DOMElement $item, \DOMDocument $xml)
    {
        if ($value != '') {
            /** @var \DOMElement $node */
            $node = $xml->createElement($nodeName, $value);

            return $item->appendChild($node);
        }

        return false;
    }

    /**
     * Get label value for select and multiselect attributes
     * Remove locale / channel in product array keys
     * Add currency for prices attributes
     * Remove html characters, encode special html characters on textarea /text attributes
     * Hack to prevent undefined index on product array if attribute mapping is not specified
     * Create url for product images
     *
     * @param  array $product
     *
     * @return mixed $newProduct
     */
    protected function formatProductArray(array $product)
    {
        /** @var array $parameters */
        $parameters = $this->stepExecution->getJobParameters()->all();
        if (!isset($parameters['filters']['structure']['locales'][0])) {
            return false;
        }
        $parameters['locale'] = $parameters['filters']['structure']['locales'][0];
        if (isset($parameters['with_media'])) {
            unset($parameters['with_media']);
        }
        /** @var array $newProduct */
        $newProduct = [];
        foreach ($product as $key => $value) {
            if (!in_array($key, $parameters)) {
                continue;
            }
            if (!isset($product[$key][0]['data'])) {
                continue;
            }
            /** @var mixed $value */
            $value         = $product[$key][0]['data'];
            $product[$key] = $value;
            /** @var array $newKey */
            $newKey = explode('-', $key);
            if (!isset($product[$key])) {
                continue;
            }
            $newProduct[$newKey[0]] = $product[$key];
            if (!isset($newKey[0])) {
                continue;
            }
            /** @var mixed $attribute */
            $attribute = $this->attributeRepository->findOneByIdentifier($newKey[0]);
            if ($attribute !== null) {
                switch ($attribute->getAttributeType()) {
                    case AttributeTypes::OPTION_MULTI_SELECT:
                    case AttributeTypes::OPTION_SIMPLE_SELECT:
                        foreach ($attribute->getOptions() as $option) {
                            if ($option->getCode() == $value) {
                                if (!isset($parameters['locale'])) {
                                    continue;
                                }
                                $newProduct[$newKey[0]] = $option->setLocale($parameters['locale'])
                                    ->getOptionValue()
                                    ->getLabel();
                                break;
                            }
                        }
                        break;
                    case AttributeTypes::TEXTAREA:
                    case AttributeTypes::TEXT:
                        $newProduct[$newKey[0]] = htmlentities(html_entity_decode($value));
                        break;
                    case AttributeTypes::PRICE_COLLECTION:
                        foreach ($value as $index => $data) {
                            if (!isset($parameters['currency'], $data['currency'])) {
                                continue;
                            }
                            if ($data['currency'] == $parameters['currency']) {
                                if (!isset($data['amount'], $parameters['decimalSeparator'])) {
                                    continue;
                                }
                                $value                  = number_format(
                                    $data['amount'],
                                    2,
                                    $parameters['decimalSeparator'],
                                    ''
                                );
                                $newProduct[$newKey[0]] = $value.' '.$parameters['currency'];
                            }
                        }
                        break;
                    case AttributeTypes::IMAGE:
                        if (!isset($parameters['pimMediaUrl'])) {
                            continue;
                        }
                        $newProduct[$newKey[0]] = rtrim(
                            $parameters['pimMediaUrl'],
                            '/'
                        )
                        .'/file_storage/catalog/'.$value;
                        break;
                }
            }
        }

        return $newProduct;
    }

    /**
     * @param $product
     * @param $parameters
     *
     * @return bool|mixed
     */
    protected function formatGoogleCategory(array $product, array $parameters)
    {
        if (!isset($parameters['suffix_category'])) {
            return false;
        }
        /** @var string $categorySuffix */
        $categorySuffix = $parameters['suffix_category'];
        if (!isset($product['categories'])) {
            return false;
        }
        /** @var array $category */
        $categories = $product['categories'];

        $categoryMatch = preg_grep('/^'.$categorySuffix.'/', $categories);

        if (empty($categoryMatch)) {
            return false;
        }

        return str_replace($categorySuffix, '', $categoryMatch[0]);
    }

    /**
     * @param array $product
     * @param array $parameters
     * @param \DOMElement $item
     * @param \DOMDocument $xml
     * @param string $field
     * @param string $value
     */
    protected function checkIfItemValueExistAndAddItToXml(
        array $product,
        array $parameters,
        \DOMElement $item,
        \DOMDocument $xml,
        string $field,
        string $value
    ) {
        if (isset($product['values'][$parameters[$value]])) {
            $this->addItemChild(
                $field,
                $product['values'][$parameters[$value]],
                $item,
                $xml
            );
        }
    }
}
