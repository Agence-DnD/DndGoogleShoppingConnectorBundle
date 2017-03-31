<?php

namespace Dnd\Bundle\GoogleShoppingConnectorBundle\Writer\File;
use Akeneo\Component\Batch\Job\RuntimeErrorException;
use Akeneo\Component\Buffer\BufferFactory;
use Akeneo\Component\Buffer\BufferInterface;
use Pim\Bundle\CatalogBundle\Entity\Locale;
use Symfony\Component\Validator\Constraints as Assert;
use Pim\Component\Connector\Writer\File\AbstractFileWriter;
use Pim\Component\Connector\Writer\File\ArchivableWriterInterface;
use Pim\Component\Connector\Writer\File\FilePathResolverInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Pim\Bundle\CatalogBundle\Repository\CurrencyRepositoryInterface;
use Pim\Bundle\CatalogBundle\AttributeType\AttributeTypes;
use Doctrine\Common\Persistence\ObjectRepository;
use Akeneo\Component\Batch\Item\InvalidItemException;
use Akeneo\Component\FileStorage\Repository\FileInfoRepositoryInterface;

class XmlProductWriter extends AbstractFileWriter implements ArchivableWriterInterface
{

    /** @var BufferInterface */
    protected $buffer;

    /** @var string */
    protected $title;

    /** @var string */
    protected $description;

    /** @var string */
    protected $websiteLink;

    /** @var string */
    protected $googleId;

    /** @var string */
    protected $googleTitle;

    /** @var string */
    protected $googleDescription;

    /** @var string */
    protected $googleCategory;

    /** @var string */
    protected $googleLink;

    /** @var string */
    protected $googleImagesLink;

    /** @var string */
    protected $googleCondition;

    /** @var string */
    protected $googleAvailability;

    /** @var string */
    protected $googlePrice;

    /** @var string */
    protected $googleGtin;

    /** @var string */
    protected $googleBrand;

    /** @var string */
    protected $googleColor;

    /** @var string */
    protected $googleGender;

    /** @var string */
    protected $googleAgeGroup;

    /** @var string */
    protected $googleMaterial;

    /** @var string */
    protected $googlePattern;

    /** @var string */
    protected $googleSize;

    /** @var string */
    protected $googlePersonalizeOne;

    /** @var string */
    protected $googlePersonalizeTwo;

    /** @var string */
    protected $googlePersonalizeThree;

    /** @var string */
    protected $googlePersonalizeFour;

    /** @var string */
    protected $googlePersonalizeFive;

    /** @var array */
    protected $writtenFiles = [];

    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    /** @var LocaleRepositoryInterface */
    protected $localeRepository;

    /** @var string */
    protected $locale;

    /** @var CurrencyRepositoryInterface */
    protected $currencyRepository;

    /** @var string */
    protected $currency;

    /** @var ObjectRepository */
    protected $categoryRepository;

    /** @var string */
    protected $pimMediaUrl;

    /** @var FileInfoRepositoryInterface */
    protected $fileInfoRepository;

    /**
     * @param FilePathResolverInterface    $filePathResolver
     * @param BufferFactory                $bufferFactory
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LocaleRepositoryInterface    $localeRepository
     * @param CurrencyRepositoryInterface  $currencyRepository
     * @param ObjectRepository             $googleRepository
     * @param FileInfoRepositoryInterface  $fileInfoRepository
     */
    public function __construct(
        FilePathResolverInterface $filePathResolver,
        BufferFactory $bufferFactory,
        AttributeRepositoryInterface $attributeRepository,
        LocaleRepositoryInterface $localeRepository,
        CurrencyRepositoryInterface $currencyRepository,
        ObjectRepository $categoryRepository,
        FileInfoRepositoryInterface $fileInfoRepository
    ) {
        parent::__construct($filePathResolver);

        $this->buffer              = $bufferFactory->create();
        $this->attributeRepository = $attributeRepository;
        $this->localeRepository    = $localeRepository;
        $this->currencyRepository  = $currencyRepository;
        $this->categoryRepository    = $categoryRepository;
        $this->fileInfoRepository   = $fileInfoRepository;
    }

    /**
     * Set the title of the channel
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get the title of the channel
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the description of the channel
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the description of the channel
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the website link of the channel
     *
     * @param string $websiteLink
     */
    public function setWebsiteLink($websiteLink)
    {
        $this->websiteLink = $websiteLink;
    }

    /**
     * Get the website link of the channel
     *
     * @return string
     */
    public function getWebsiteLink()
    {
        return $this->websiteLink;
    }

    /**
     * @return string
     */
    public function getGoogleColor()
    {
        return $this->googleColor;
    }

    /**
     * @param string $googleColor
     */
    public function setGoogleColor($googleColor)
    {
        $this->googleColor = $googleColor;
    }

    /**
     * @return string
     */
    public function getGoogleGender()
    {
        return $this->googleGender;
    }

    /**
     * @param string $googleGender
     */
    public function setGoogleGender($googleGender)
    {
        $this->googleGender = $googleGender;
    }

    /**
     * @return string
     */
    public function getGoogleAgeGroup()
    {
        return $this->googleAgeGroup;
    }

    /**
     * @param string $googleAgeGroup
     */
    public function setGoogleAgeGroup($googleAgeGroup)
    {
        $this->googleAgeGroup = $googleAgeGroup;
    }

    /**
     * @return string
     */
    public function getGoogleMaterial()
    {
        return $this->googleMaterial;
    }

    /**
     * @param string $googleMaterial
     */
    public function setGoogleMaterial($googleMaterial)
    {
        $this->googleMaterial = $googleMaterial;
    }

    /**
     * @return string
     */
    public function getGooglePattern()
    {
        return $this->googlePattern;
    }

    /**
     * @param string $googlePattern
     */
    public function setGooglePattern($googlePattern)
    {
        $this->googlePattern = $googlePattern;
    }

    /**
     * @return string
     */
    public function getGoogleSize()
    {
        return $this->googleSize;
    }

    /**
     * @param string $googleSize
     */
    public function setGoogleSize($googleSize)
    {
        $this->googleSize = $googleSize;
    }

    /**
     * @return string
     */
    public function getGooglePersonalizeOne()
    {
        return $this->googlePersonalizeOne;
    }

    /**
     * @param string $googlePersonalizeOne
     */
    public function setGooglePersonalizeOne($googlePersonalizeOne)
    {
        $this->googlePersonalizeOne = $googlePersonalizeOne;
    }

    /**
     * @return string
     */
    public function getGooglePersonalizeTwo()
    {
        return $this->googlePersonalizeTwo;
    }

    /**
     * @param string $googlePersonalizeTwo
     */
    public function setGooglePersonalizeTwo($googlePersonalizeTwo)
    {
        $this->googlePersonalizeTwo = $googlePersonalizeTwo;
    }

    /**
     * @return string
     */
    public function getGooglePersonalizeThree()
    {
        return $this->googlePersonalizeThree;
    }

    /**
     * @param string $googlePersonalizeThree
     */
    public function setGooglePersonalizeThree($googlePersonalizeThree)
    {
        $this->googlePersonalizeThree = $googlePersonalizeThree;
    }

    /**
     * @return string
     */
    public function getGooglePersonalizeFour()
    {
        return $this->googlePersonalizeFour;
    }

    /**
     * @param string $googlePersonalizeFour
     */
    public function setGooglePersonalizeFour($googlePersonalizeFour)
    {
        $this->googlePersonalizeFour = $googlePersonalizeFour;
    }

    /**
     * @return string
     */
    public function getGooglePersonalizeFive()
    {
        return $this->googlePersonalizeFive;
    }

    /**
     * @param string $googlePersonalizeFive
     */
    public function setGooglePersonalizeFive($googlePersonalizeFive)
    {
        $this->googlePersonalizeFive = $googlePersonalizeFive;
    }

    /**
     * @return string
     */
    public function getGoogleBrand()
    {
        return $this->googleBrand;
    }

    /**
     * @param string $googleBrand
     */
    public function setGoogleBrand($googleBrand)
    {
        $this->googleBrand = $googleBrand;
    }

    /**
     * @return string
     */
    public function getGoogleGtin()
    {
        return $this->googleGtin;
    }

    /**
     * @param string $googleGtin
     */
    public function setGoogleGtin($googleGtin)
    {
        $this->googleGtin = $googleGtin;
    }

    /**
     * @return string
     */
    public function getGooglePrice()
    {
        return $this->googlePrice;
    }

    /**
     * @param string $googlePrice
     */
    public function setGooglePrice($googlePrice)
    {
        $this->googlePrice = $googlePrice;
    }

    /**
     * @return string
     */
    public function getGoogleAvailability()
    {
        return $this->googleAvailability;
    }

    /**
     * @param string $googleAvailability
     */
    public function setGoogleAvailability($googleAvailability)
    {
        $this->googleAvailability = $googleAvailability;
    }

    /**
     * @return string
     */
    public function getGoogleCondition()
    {
        return $this->googleCondition;
    }

    /**
     * @param string $googleCondition
     */
    public function setGoogleCondition($googleCondition)
    {
        $this->googleCondition = $googleCondition;
    }

    /**
     * @return string
     */
    public function getGoogleImagesLink()
    {
        return $this->googleImagesLink;
    }

    /**
     * @param string $googleImagesLink
     */
    public function setGoogleImagesLink($googleImagesLink)
    {
        $this->googleImagesLink = $googleImagesLink;
    }

    /**
     * @return string
     */
    public function getGoogleLink()
    {
        return $this->googleLink;
    }

    /**
     * @param string $googleLink
     */
    public function setGoogleLink($googleLink)
    {
        $this->googleLink = $googleLink;
    }

    /**
     * @return string
     */
    public function getGoogleDescription()
    {
        return $this->googleDescription;
    }

    /**
     * @param string $googleDescription
     */
    public function setGoogleDescription($googleDescription)
    {
        $this->googleDescription = $googleDescription;
    }

    /**
     * @return string
     */
    public function getGoogleTitle()
    {
        return $this->googleTitle;
    }

    /**
     * @param string $googleTitle
     */
    public function setGoogleTitle($googleTitle)
    {
        $this->googleTitle = $googleTitle;
    }

    /**
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param string $googleId
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }

    /**
     * {@inheritdoc}
     */
    public function getWrittenFiles()
    {
        return $this->writtenFiles;
    }

    /**
     * Set locale
     *
     * @param string $localeCode Locale code
     *
     * @return $this
     */
    public function setLocale($localeCode)
    {
        $this->locale = $localeCode;

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
     * Set currency
     *
     * @param string $currencyCode Currency code
     *
     * @return $this
     */
    public function setCurrency($currencyCode)
    {
        $this->currency = $currencyCode;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string Currency code
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Return PimMediaUrl with rtrim to be sur that there is a / at the end of the url
     * @return string
     */
    public function getPimMediaUrl()
    {
        return rtrim($this->pimMediaUrl, '/') . '/';
    }

    /**
     * @param string $pimMediaUrl
     * @return XmlProductWriter
     */
    public function setPimMediaUrl($pimMediaUrl)
    {
        $this->pimMediaUrl = $pimMediaUrl;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $items)
    {

        if (false === file_exists($this->getPath())) {
            $xml = new \DOMDocument('1.0', 'utf-8');
            $rss = $xml->createElement('rss');
            $rss->setAttribute('xmlns:g', "http://base.google.com/ns/1.0");
            $rss->setAttribute('version', "1.0");
            $channel = $xml->createElement('channel');
            $channelTitle = $xml->createElement('title', $this->getTitle());
            $channel->appendChild($channelTitle);
            $channelLink = $xml->createElement('link', $this->getWebsiteLink());
            $channel->appendChild($channelLink);
            $channelDescription = $xml->createElement('description', $this->getDescription());
            $channel->appendChild($channelDescription);
            $rss->appendChild($channel);
            $xml->appendChild($rss);
        } else {
            $xml = new \DOMDocument('1.0','utf-8');
            $content = file_get_contents($this->getPath());
            $content = html_entity_decode($content);
            $xml->formatOutput = true;
            $xml->preserveWhiteSpace = false;
            $xml->loadXML($content);
            $channel = $xml->getElementsByTagName("channel")->item(0);
        }

        foreach ($items as $product) {
            $product['product'] = $this->formatProductArray($product['product']);
            $googleCategory = $product['product'][$this->getGoogleCategory($product)];

            if (!$googleCategory) {
                $this->setItemError($product, 'job_execution.summary.undefined_google_category');
            }

            $item = $xml->createElement('item');

            $this->addItemChild('g:id', $product['product'][$this->getGoogleId()], $item, $xml);

            $this->addItemChild('g:title', $product['product'][$this->getGoogleTitle()], $item, $xml);

            $this->addItemChild('g:description', $product['product'][$this->getGoogleDescription()], $item, $xml);

            $this->addItemChild('g:google_product_category', htmlentities($googleCategory), $item, $xml);

            $this->addItemChild('g:link', $product['product'][$this->getGoogleLink()], $item, $xml);

            $this->addItemChild('g:image_link', $product['product'][$this->getGoogleImagesLink()], $item, $xml);

            $this->addItemChild('g:condition', $this->getGoogleCondition(), $item, $xml);

            $this->addItemChild('g:availability', $this->getGoogleAvailability(), $item, $xml);

            $this->addItemChild('g:price', $product['product'][$this->getGooglePrice()], $item, $xml);

            $this->addItemChild('g:gtin', $product['product'][$this->getGoogleGtin()], $item, $xml);

            $this->addItemChild('g:brand', $product['product'][$this->getGoogleBrand()], $item, $xml);

            $this->addItemChild('g:color', $product['product'][$this->getGoogleColor()], $item, $xml);

            $this->addItemChild('g:gender', $product['product'][$this->getGoogleGender()], $item, $xml);

            $this->addItemChild('g:age_group', $product['product'][$this->getGoogleAgeGroup()], $item, $xml);

            $this->addItemChild('g:material', $product['product'][$this->getGoogleMaterial()], $item, $xml);

            $this->addItemChild('g:pattern', $product['product'][$this->getGooglePattern()], $item, $xml);

            $this->addItemChild('g:size', $product['product'][$this->getGoogleSize()], $item, $xml);

            $this->addItemChild('g:custom_label_0', $product['product'][$this->getGooglePersonalizeOne()], $item, $xml);

            $this->addItemChild('g:custom_label_1', $product['product'][$this->getGooglePersonalizeTwo()], $item, $xml);

            $this->addItemChild('g:custom_label_2', $product['product'][$this->getGooglePersonalizeThree()], $item, $xml);

            $this->addItemChild('g:custom_label_3', $product['product'][$this->getGooglePersonalizeFour()], $item, $xml);

            $this->addItemChild('g:custom_label_4', $product['product'][$this->getGooglePersonalizeFive()], $item, $xml);

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
     * @param $nodeName
     * @param $value
     * @param $item
     * @param $xml
     *
     * @return bool
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
     * @return array $newProduct
     */
    protected function formatProductArray($product)
    {
        $newProduct = [];
        foreach ($product as $key => $value) {
            $newKey = explode('-', $key);
            $newProduct[$newKey[0]] = $product[$key];
            $attribute = $this->attributeRepository->findOneByIdentifier($newKey[0]);
            if ($attribute !== NULL) {
                if (in_array($attribute->getAttributeType(), [AttributeTypes::OPTION_MULTI_SELECT, AttributeTypes::OPTION_SIMPLE_SELECT])) {
                    foreach ($attribute->getOptions() as $option) {
                        if ($option->getCode() == $value) {
                            $newProduct[$newKey[0]] = $option->setLocale($this->getLocale())->getOptionValue()->getLabel();
                            break;
                        }
                    }
                } elseif ($attribute->getAttributeType() == AttributeTypes::PRICE_COLLECTION) {
                    $newProduct[$newKey[0]] = $value . ' ' . $this->getCurrency();
                } elseif (in_array($attribute->getAttributeType(), [AttributeTypes::TEXT, AttributeTypes::TEXTAREA])) {
                    $newProduct[$newKey[0]] = htmlentities(html_entity_decode($value));
                } elseif ($attribute->getAttributeType() == AttributeTypes::IMAGE) {
                    $fileName = basename($value);
                    $file     = $this->fileInfoRepository->findOneBy(['originalFilename' => $fileName]);
                    if ($file !== null) {
                        $newProduct[$newKey[0]] = $this->getPimMediaUrl() . 'file_storage/catalog/' . $file->getKey();
                    }
                }
            }
        }
        $newProduct[''] = '';
        return $newProduct;
    }

    /**
     * Get configuration fields for the export
     *
     * @return array
     */
    public function getConfigurationFields()
    {
        return
            array_merge(
                parent::getConfigurationFields(),
                [
                    'title' => [
                        'options' => [
                            'label' => 'dnd_google_shopping_connector.export.title.label',
                            'help'  => 'dnd_google_shopping_connector.export.title.help'
                        ]
                    ],
                    'description' => [
                        'options' => [
                            'label' => 'dnd_google_shopping_connector.export.description.label',
                            'help'  => 'dnd_google_shopping_connector.export.description.help'
                        ]
                    ],
                    'websiteLink' => [
                        'options' => [
                            'label' => 'dnd_google_shopping_connector.export.websiteLink.label',
                            'help'  => 'dnd_google_shopping_connector.export.websiteLink.help'
                        ]
                    ],
                    'googleId' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleId.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleId.help'
                        ]
                    ],
                    'googleTitle' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleTitle.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleTitle.help'
                        ]
                    ],
                    'googleDescription' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleDescription.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleDescription.help'
                        ]
                    ],
                    'googleLink' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleLink.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleLink.help'
                        ]
                    ],
                    'googleImagesLink' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label' => 'dnd_google_shopping_connector.export.googleImagesLink.label',
                            'help'  => 'dnd_google_shopping_connector.export.googleImagesLink.help'
                        ]
                    ],
                    'googleCondition' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => [
                                'new'         => 'dnd_google_shopping_connector.export.googleCondition.choice.new',
                                'refurbished' => 'dnd_google_shopping_connector.export.googleCondition.choice.refurbished',
                                'used'        => 'dnd_google_shopping_connector.export.googleCondition.choice.used',
                            ],
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleCondition.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleCondition.help'
                        ]
                    ],
                    'googleAvailability' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => [
                                'preorder'     => 'dnd_google_shopping_connector.export.googleAvailability.choice.preorder',
                                'in stock'     => 'dnd_google_shopping_connector.export.googleAvailability.choice.in_stock',
                                'out of stock' => 'dnd_google_shopping_connector.export.googleAvailability.choice.out_of_stock',
                            ],
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleAvailability.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleAvailability.help'
                        ]
                    ],
                    'googlePrice' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googlePrice.label',
                            'help'     => 'dnd_google_shopping_connector.export.googlePrice.help'
                        ]
                    ],
                    'googleGtin' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleGtin.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleGtin.help'
                        ]
                    ],
                    'googleBrand' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleBrand.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleBrand.help'
                        ]
                    ],
                    'googleColor' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleColor.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleColor.help'
                        ]
                    ],
                    'googleGender' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleGender.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleGender.help'
                        ]
                    ],
                    'googleAgeGroup' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleAgeGroup.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleAgeGroup.help'
                        ]
                    ],
                    'googleMaterial' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleMaterial.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleMaterial.help'
                        ]
                    ],
                    'googlePattern' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googlePattern.label',
                            'help'     => 'dnd_google_shopping_connector.export.googlePattern.help'
                        ]
                    ],
                    'googleSize' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googleSize.label',
                            'help'     => 'dnd_google_shopping_connector.export.googleSize.help'
                        ]
                    ],
                    'googlePersonalizeOne' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googlePersonalizeOne.label',
                            'help'     => 'dnd_google_shopping_connector.export.googlePersonalizeOne.help'
                        ]
                    ],
                    'googlePersonalizeTwo' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googlePersonalizeTwo.label',
                            'help'     => 'dnd_google_shopping_connector.export.googlePersonalizeTwo.help'
                        ]
                    ],
                    'googlePersonalizeThree' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googlePersonalizeThree.label',
                            'help'     => 'dnd_google_shopping_connector.export.googlePersonalizeThree.help'
                        ]
                    ],
                    'googlePersonalizeFour' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googlePersonalizeFour.label',
                            'help'     => 'dnd_google_shopping_connector.export.googlePersonalizeFour.help'
                        ]
                    ],
                    'googlePersonalizeFive' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getAttributesChoices(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.googlePersonalizeFive.label',
                            'help'     => 'dnd_google_shopping_connector.export.googlePersonalizeFive.help'
                        ]
                    ],
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
                    'currency' => [
                        'type'    => 'choice',
                        'options' => [
                            'choices'  => $this->getCurrencyCodes(),
                            'required' => true,
                            'select2'  => true,
                            'label'    => 'dnd_google_shopping_connector.export.currency.label',
                            'help'     => 'dnd_google_shopping_connector.export.currency.help'
                        ]
                    ],
                    'pimMediaUrl' => [
                        'options' => [
                            'label'    => 'dnd_google_shopping_connector.export.pimMediaUrl.label',
                            'help'     => 'dnd_google_shopping_connector.export.pimMediaUrl.help'
                        ]
                    ],
                ]
            );
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
     * Get currency codes for select option
     *
     * @return array
     */
    protected function getCurrencyCodes()
    {
        $choices = [];
        foreach ($this->currencyRepository->getActivatedCurrencies() as $currency) {
            $choices[$currency->getCode()] = $currency->getCode();
        }
        return $choices;
    }

    /**
     * Retrieve attributes code for select option
     *
     * @return array[] $choices
     */
    protected function getAttributesChoices()
    {
        $choices = [];
        $choices[''] = '';
        foreach ($this->attributeRepository->getAttributesAsArray() as $attribute) {
            $choices[$attribute['code']] = $attribute['code'];
        }
        return $choices;
    }

    /**
     * @param $product
     *
     * @return mixed
     */
    protected function getGoogleCategory($product)
    {
        return $googleCategory = $product['product']['categories'];

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
}
