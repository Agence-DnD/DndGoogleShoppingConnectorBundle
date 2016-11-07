GoogleShoppingConnectorBundle
=============================

Google Shopping Connector for Akeneo PIM >= 1.5

This connector allows you to export your products into XML for Google Shopping.

## Requirements

* Akeneo PIM >= 1.5
* Akeneo labs CustomEntityBundle >= 1.7

## Connector installation on Akeneo PIM

If it's not already done, install [Akeneo PIM](https://github.com/akeneo/pim-community-standard).

Get composer (with command line):
```console
$ cd /my/pim/installation/dir
$ curl -sS https://getcomposer.org/installer | php
```

First of all, install [Akeneo labs CustomEntityBundle](https://github.com/akeneo-labs/CustomEntityBundle).

Then, install DnD-GoogleShoppingConnectorBundle with composer:

In your ```composer.json```, add the following code:

* In `require`:

```json
{
    "agencednd/google-shopping-connector-bundle": "1.0.*"
}
```

* In `post-install-cmd`, under "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters"`:

```json
{
        "post-install-cmd" : {
              "Dnd\\Bundle\\GoogleShoppingConnectorBundle\\ComposerScripts::getProductValueByPimEdition"
        }
}
```

* In `post-update-cmd`, under "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters"`:

```json
{
        "post-update-cmd" : {
              "Dnd\\Bundle\\GoogleShoppingConnectorBundle\\ComposerScripts::getProductValueByPimEdition"
        }
}
```

Next, enter the following command line:
```console
$ php composer.phar update
```

Enable the bundle in ```app/AppKernel.php``` file, in the ```registerBundles``` function, before the line ```return $bundles```:
```php
$bundles[] = new Dnd\Bundle\GoogleShoppingConnectorBundle\DndGoogleShoppingConnectorBundle();
```

Next, in the ```app/config/config.yml``` file, add the following line under the _pim_reference_data_ node :
```yml
    googleCategory:
        class: Dnd\Bundle\GoogleShoppingConnectorBundle\Entity\GoogleCategory
        type: simple
```

**If you use Akeneo PIM Enterprise Edition:**


Add this line in the _mapping_overrides_ node, under all the overrides : 
```yml
        -
            original: PimEnterprise\Bundle\CatalogBundle\Model\ProductValue
            override: Dnd\Bundle\GoogleShoppingConnectorBundle\Model\ProductValue
```

_End Akeneo PIM Enterprise Edition Specific_

**If you use Akeneo PIM Community Edition:**

Add this line in the _mapping_overrides_ node, under all the overrides : 
```yml
        -
            original: Pim\Component\Catalog\Model\ProductValue
            override: Dnd\Bundle\GoogleShoppingConnectorBundle\Model\ProductValue
```

_End Akeneo PIM Community Edition Specific_

Finally, enter the following commands line:
```console
$ php app/console doctrine:schema:update --force
```

## Configuration

###### Create symlink for your images

If you manage your images in the PIM, **you must create a symlink of the _app/file_storage folder_ into the _web folder_** :
```console
$ ln -s /my/pim/installation/dir/web/file_storage /my/pim/installation/dir/app/file_storage
```

###### Import

* Go to _Collect_ > _Import profiles_ and then create your _GoogleShoppingConnectorBundle_ import categories profile
* Download the file of the taxonomies **Taxonomy _with numeric IDs_ in an _Excel Spreadsheet (.xls)_** on [Google Website](https://support.google.com/merchants/answer/160081?hl=en&ref_topic=3404778)
* Open this file in an editor like Open Office and save it as a **CSV file**
* Import this file with the previously created import profile

###### PIM UI

* As you can see, a new tab Reference data appeared which allows you to manage the Google categories (previously imported)
* Create a new Reference data simple select attribute and select googleCategory in the field 'Choose the reference data type'
* Add the new google_category attribute to your families
* Classify your products with the new google_category attribute

###### Export

* Go to _Spread_ > _Export profiles_ and then create your _GoogleShoppingConnectorBundle_ export products for Google shopping profile
* Fill the informations of your channel
* Map your attributes to Google Shopping attributes (leave empty if you don't use one of them, see the [Documentation](https://support.google.com/merchants/answer/1344057?hl=en&ref_topic=3404778) to know the required attributes)
* You must map the field Product Category with the google_category attribute

## Roadmap

* Don't stop google export if one of the mapped attributes is not set
* Show the name of the Google Categories in the grid depending of the PIM user default locale

## About us
Founded by lovers of innovation and design, [Agence Dn'D] (http://www.dnd.fr) assists companies for 11 years in the creation and development of customized digital (open source) solutions for web and E-commerce.
