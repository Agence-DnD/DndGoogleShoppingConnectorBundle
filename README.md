GoogleShoppingConnectorBundle
=============================

Google Shopping Connector for Akeneo PIM >= 1.5

This connector allows you to export your products into XML for Google Shopping.

## Requirements

| GoogleShoppingConnectorBundle   | Akeneo PIM Community Edition |
|:-------------------------------:|:----------------------------:|
| v1.3.*                          | v2.*                         |
| v1.2.*                          | v1.7.*                       |
| v1.1.*                          | v1.6.*                       |
| v1.0.*                          | v1.5.*                       |

## Connector installation on Akeneo PIM

If it's not already done, install [Akeneo PIM](https://github.com/akeneo/pim-community-standard).

Get composer (with command line):
```console
$ cd /my/pim/installation/dir
$ curl -sS https://getcomposer.org/installer | php
```

Then, install DnD-GoogleShoppingConnectorBundle with composer:

In your ```composer.json```, add the following code:

* In `require`:

```json
{
    "agencednd/google-shopping-connector-bundle": "1.3.*"
}
```

Next, enter the following command line:
```console
$ php composer.phar require agence-dnd/google-shopping-connector-bundle
```

Enable the bundle in ```app/AppKernel.php``` file, in the ```registerBundles``` function, before the line ```return $bundles```:
```php
$bundles[] = new Dnd\Bundle\GoogleShoppingConnectorBundle\DndGoogleShoppingConnectorBundle();
```

Add the route in ```app/config/routing.yml```file, under the _pim_reference_data_ node:
```yml
dnd_google_shopping_connector:
    prefix: /google-shopping-connector
    resource: "@DndGoogleShoppingConnectorBundle/Resources/config/routing.yml"
```

## Configuration

###### Create symlink for your images

If you manage your images in the PIM, **you must create a symlink of the _app/file_storage folder_ into the _web folder_** :
```console
$ ln -s /my/pim/installation/dir/web/file_storage /my/pim/installation/dir/app/file_storage
```

###### Import

* Go to _Collect_ > _Import profiles_ and then create your _GoogleShoppingConnectorBundle_ import categories profile
* Select the category you want to import the google categories in (we advice you to create a new tree and a new channel dedicated to google shopping)
* Download the file of the taxonomies **Taxonomy _with numeric IDs_ in an _Excel Spreadsheet (.xls)_** on [Google Website](https://support.google.com/merchants/answer/160081?hl=en&ref_topic=3404778)
* Open this file in an editor like Open Office and save it as a **XLSX file**
* Import this file with the previously created import profile

###### Export

* Go to _Spread_ > _Export profiles_ and then create your _GoogleShoppingConnectorBundle_ export products for Google shopping profile
* Fill the informations of your channel
* Map your attributes to Google Shopping attributes (leave empty if you don't use one of them, see the [Documentation](https://support.google.com/merchants/answer/1344057?hl=en&ref_topic=3404778) to know the required attributes)

## About us
Founded by lovers of innovation and design, [Agence Dn'D](https://www.dnd.fr) assists companies in the creation and development of customized digital (open source) solutions for web and E-commerce since 2004.
