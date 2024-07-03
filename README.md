# Delete Order User Guide

## 1. How to install

### Method 1: Install ready-to-paste package

- Download the latest version at [Delete Order for Magento 2](https://commercemarketplace.adobe.com/md-module-delete-orders.html)

### Method 2: Install via composer [Recommend]

Run the following command in Magento 2 root folder

```
composer require md_module/delete-orders

php bin/magento maintenance:enable
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento maintenance:disable
php bin/magento cache:flush
```

## 2. How to use

- Please refer to the `Delete Orders User Guide.pdf` file for instructions on how to use the extension.

## 3. Get Support

- Feel free to [contact us](mailto:mufaddaldhansurawala@gmail.com) if you have any further questions.
- Like this project, Give us a **Star**
