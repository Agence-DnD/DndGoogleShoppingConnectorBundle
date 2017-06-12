<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Writer\File\Xml;


use Pim\Component\Connector\Writer\File\AbstractFileWriter;
use Pim\Component\Connector\Writer\File\ArchivableWriterInterface;
use Akeneo\Component\Batch\Job\RuntimeErrorException;
use Akeneo\Component\Buffer\BufferFactory;
use Akeneo\Component\Buffer\BufferInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Component\Catalog\Repository\CurrencyRepositoryInterface;
use Pim\Component\Catalog\AttributeTypes;
use Doctrine\Common\Persistence\ObjectRepository;
use Akeneo\Component\FileStorage\Repository\FileInfoRepositoryInterface;

/**
 * Class XmlProductWriter
 *
 * @author                 Agence Dn'D <contact@dnd.fr>
 * @copyright              Copyright (c) 2017 Agence Dn'D
 * @license                http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link                   http://www.dnd.fr/
 */
class XmlProductWriter extends AbstractFileWriter implements ArchivableWriterInterface
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

    /**
     * @param BufferFactory                $bufferFactory
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LocaleRepositoryInterface    $localeRepository
     * @param CurrencyRepositoryInterface  $currencyRepository
     * @param FileInfoRepositoryInterface  $fileInfoRepository
     */
    public function __construct(
        BufferFactory $bufferFactory,
        AttributeRepositoryInterface $attributeRepository,
        LocaleRepositoryInterface $localeRepository,
        CurrencyRepositoryInterface $currencyRepository,
        FileInfoRepositoryInterface $fileInfoRepository
    ) {
        parent::__construct();

        $this->buffer = $bufferFactory->create();
        $this->attributeRepository = $attributeRepository;
        $this->localeRepository = $localeRepository;
        $this->currencyRepository = $currencyRepository;
        $this->fileInfoRepository = $fileInfoRepository;
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
        $parameters = $this->stepExecution->getJobParameters()->all();

        if (false === file_exists($this->getPath())) {
            $xml = new \DOMDocument('1.0', 'utf-8');
            $rss = $xml->createElement('rss');
            $rss->setAttribute('xmlns:g', "http://base.google.com/ns/1.0");
            $rss->setAttribute('version', "1.0");
            $channel = $xml->createElement('channel');
            $channelTitle = $xml->createElement('title', $parameters['channelTitle']);
            $channel->appendChild($channelTitle);
            $channelLink = $xml->createElement('link', $parameters['websiteLink']);
            $channel->appendChild($channelLink);
            $channelDescription = $xml->createElement('description', $parameters['description']);
            $channel->appendChild($channelDescription);
            $rss->appendChild($channel);
            $xml->appendChild($rss);
        } else {
            $xml = new \DOMDocument('1.0', 'utf-8');
            $content = file_get_contents($parameters['filePath']);
            $content = html_entity_decode($content);
            $xml->formatOutput = true;
            $xml->preserveWhiteSpace = false;
            $xml->loadXML($content);
            $channel = $xml->getElementsByTagName("channel")->item(0);
        }

        foreach ($items as $product) {
            $product['values'] = $this->formatProductArray($product['values']);

            $item = $xml->createElement('item');

            $this->addItemChild('g:id', $product['values'][$parameters['googleId']], $item, $xml);
            $this->addItemChild('g:title', $product['values'][$parameters['googleTitle']], $item, $xml);
            $this->addItemChild('g:description', $product['values'][$parameters['googleDescription']], $item, $xml);
            $this->addItemChild('g:google_product_category', htmlentities($this->formatGoogleCategory($product)), $item, $xml);
            $this->addItemChild('g:link', $product['values'][$parameters['googleLink']], $item, $xml);
            $this->addItemChild('g:image_link', $product['values'][$parameters['googleImagesLink']], $item, $xml);
            $this->addItemChild('g:condition', $parameters['googleCondition'], $item, $xml);
            $this->addItemChild('g:availability', $parameters['googleAvailability'], $item, $xml);
            $this->addItemChild('g:price', $product['values'][$parameters['googlePrice']], $item, $xml);
            $this->addItemChild('g:gtin', $product['values'][$parameters['googleGtin']], $item, $xml);
            $this->addItemChild('g:brand', $product['values'][$parameters['googleBrand']], $item, $xml);
            $this->addItemChild('g:color', $product['values'][$parameters['googleColor']], $item, $xml);
            $this->addItemChild('g:gender', $parameters['googleGender'], $item, $xml);
            $this->addItemChild('g:age_group', $parameters['googleAgeGroup'], $item, $xml);
            $this->addItemChild('g:material', $product['values'][$parameters['googleMaterial']], $item, $xml);
            $this->addItemChild('g:pattern', $product['values'][$parameters['googlePattern']], $item, $xml);
            $this->addItemChild('g:size', $product['values'][$parameters['googleSize']], $item, $xml);
            $this->addItemChild('g:custom_label_0', $product['values'][$parameters['googlePersonalizeOne']], $item, $xml);
            $this->addItemChild('g:custom_label_1', $product['values'][$parameters['googlePersonalizeTwo']], $item, $xml);
            $this->addItemChild('g:custom_label_2', $product['values'][$parameters['googlePersonalizeThree']], $item, $xml);
            $this->addItemChild('g:custom_label_3', $product['values'][$parameters['googlePersonalizeFour']], $item, $xml);
            $this->addItemChild('g:custom_label_4', $product['values'][$parameters['googlePersonalizeFive']], $item, $xml);

            $channel->appendChild($item);

            $xml->formatOutput = true;
        }

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
     * @param string       $nodeName
     * @param string       $value
     * @param \DomElement  $item
     * @param \DomDocument $xml
     *
     * @return \DOMElement
     */
    protected function addItemChild($nodeName, $value, $item, $xml)
    {

        if ($value != '') {
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
     * @return array $newProduct
     */
    protected function formatProductArray($product)
    {
        $parameters = $this->stepExecution->getJobParameters()->all();
        $parameters['locale'] = $parameters['filters']['structure']['locales'][0];
        unset($parameters['with_media']);

        $newProduct = [];
        foreach ($product as $key => $value) {
            if (!in_array($key, $parameters)) {
                continue;
            }
            $value = $product[$key][0]['data'];
            $product[$key] = $value;
            $newKey = explode('-', $key);
            $newProduct[$newKey[0]] = $product[$key];
            $attribute = $this->attributeRepository->findOneByIdentifier($newKey[0]);
            if ($attribute !== null) {
                switch ($attribute->getAttributeType()) {
                    case AttributeTypes::OPTION_MULTI_SELECT:
                    case AttributeTypes::OPTION_SIMPLE_SELECT:
                        foreach ($attribute->getOptions() as $option) {
                            if ($option->getCode() == $value) {
                                $newProduct[$newKey[0]] = $option->setLocale($parameters['locale'])->getOptionValue()->getLabel();
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
                            if ($data['currency'] == $parameters['currency']) {
                                $value = number_format($data['data'], 2, $parameters['decimalSeparator'], '');
                                $newProduct[$newKey[0]] = $value.' '.$parameters['currency'];
                            }
                        }
                        break;
                    case AttributeTypes::IMAGE:
                        $fileName = basename($value['originalFilename']);
                        $file = $this->fileInfoRepository->findOneBy(['originalFilename' => $fileName]);
                        if ($file !== null) {
                            $newProduct[$newKey[0]] = rtrim($parameters['pimMediaUrl'], '/').'/file_storage/catalog/'.$file->getKey();
                        }
                        break;
                }
            }
        }
        $parameters = [
            $parameters['googleId']               => '',
            $parameters['googleTitle']            => '',
            $parameters['googleDescription']      => '',
            $parameters['googleLink']             => '',
            $parameters['googleImagesLink']       => '',
            $parameters['googlePrice']            => '',
            $parameters['googleGtin']             => '',
            $parameters['googleBrand']            => '',
            $parameters['googleColor']            => '',
            $parameters['googleGender']           => '',
            $parameters['googleAgeGroup']         => '',
            $parameters['googleMaterial']         => '',
            $parameters['googleSize']             => '',
            $parameters['googlePattern']          => '',
            $parameters['googlePersonalizeOne']   => '',
            $parameters['googlePersonalizeTwo']   => '',
            $parameters['googlePersonalizeThree'] => '',
            $parameters['googlePersonalizeFour']  => '',
            $parameters['googlePersonalizeFive']  => '',
        ];

        $missingValues = array_diff_key($parameters, $newProduct);

        $newProduct += $missingValues;

        $newProduct[''] = '';

        return $newProduct;
    }

    /**
     * @param $product
     *
     * @return mixed
     */
    protected function formatGoogleCategory($product)
    {
        $category = $product['categories'][0];

        return str_replace('google_category_', '', $category);
    }
}
