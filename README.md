# NOOE Connector for Magento 2

- **Requires at least:** M 2.3
- **Tested up to:** 2.4
- **Requires PHP:** 7.1
- **Stable tag:** 1.0.0
- **License:** GPL-3.0
- **License URI:** https://opensource.org/licenses/gpl-3.0

## Overview

<p align="center">
  <img src="https://nooestores.com/assets/images/visore.jpg">
</p>

The first Phygital Storein the world
The shopping experience has never been so engaging

## Plugin installation

Follow the instruction below if you want to install NOOE for Magento 2 using Git.

1.A) Clone the git repository in the Magento 2 `app/code` folder using:

    git clone git@github.com:Tun2U/Nooe-M2Connector.git Nooe/Connector

Follow the instruction below if you want to install NOOE for Magento 2 using Composer.

1.B) Connect via SSH and run the following commands

    composer require nooe/module-m2-connector

In case you wish to contribute to the plugin, fork the `dev` branch rather than cloning it, and create a pull request via Github. For further information please read the section "Become a contributor" of this document.

2.) Set the correct directory permissions:

    chmod -R 755 app/code/Nooe/Connector

Depending on your server configuration, it might be necessary to set whole write permissions (777) to the files and folders above.
You can also start testing with lower permissions due to security reasons (644 for example) as long as your php process can write to those files.

3.) Connect via SSH and run the following commands (make sure to run them as the user who owns the Magento files!)

    php bin/magento module:enable Nooe_Connector
    php bin/magento maintenance:enable
    php bin/magento setup:upgrade
    php bin/magento setup:static-content:deploy
    php bin/magento maintenance:disable
    php bin/magento cache:clean
    php bin/magento cache:flush

4.) Configure the plugin

## Frequently Asked Questions

### Where can I find NOOE documentation and user guides?

For help setting up and configuring NOOE plugin please refer to our [user guide](https://www.nooestores.com)

### Where can I get support?

To make a support request to NOOE, use [our helpdesk](https://www.nooestores.com).

## Become a contributor

NOOE for Magento 2 is available under license (GPL-3.0). If you want to contribute code (features or bugfixes), you have to create a pull request via Github and include valid license information.

The `master` branch contains the latest stable version of the plugin. The `dev` branch contains the version under development.
All Pull requests must be made on the `dev` branch and must be validated by reviewers working at NOOE.

## Changelog

The changelog and all available commits are located under [CHANGELOG](CHANGELOG).
