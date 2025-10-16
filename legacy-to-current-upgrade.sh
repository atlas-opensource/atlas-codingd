#!/bin/bash

# This script upgrades the Yii2 project to the latest version.
# It is designed to be run on Debian, Ubuntu, RedHat, Fedora, and CentOS.

# Function to print error messages and exit
error_exit() {
    echo "Error: $1" >&2
    exit 1
}

# Function to detect the OS
detect_os() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS=$ID
    else
        error_exit "Cannot detect OS."
    fi
}

# Function to install prerequisites and PHP 8.3 on Debian/Ubuntu
install_debian() {
    echo "Installing prerequisites for Debian/Ubuntu..."
    apt-get update || error_exit "Failed to update package list."
    apt-get install -y software-properties-common apt-transport-https lsb-release ca-certificates wget || error_exit "Failed to install prerequisites."
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg || error_exit "Failed to add repository signing key."
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list || error_exit "Failed to add repository."
    apt-get update || error_exit "Failed to update package list."
    apt-get install -y php8.3 php8.3-cli php8.3-common php8.3-mysql php8.3-xml php8.3-gd php8.3-mbstring php8.3-curl php8.3-zip php8.3-fpm || error_exit "Failed to install PHP 8.3."
    update-alternatives --set php /usr/bin/php8.3 || error_exit "Failed to set PHP 8.3 as default."
}

# Function to install prerequisites and PHP 8.3 on RedHat/CentOS/Fedora
install_redhat() {
    echo "Installing prerequisites for RedHat/CentOS/Fedora..."
    yum install -y epel-release || error_exit "Failed to install EPEL release."
    yum install -y yum-utils || error_exit "Failed to install yum-utils."
    yum install -y http://rpms.remirepo.net/enterprise/remi-release-7.rpm || error_exit "Failed to install Remi release."
    yum-config-manager --enable remi-php83 || error_exit "Failed to enable Remi PHP 8.3 repository."
    yum install -y php php-cli php-common php-mysql php-xml php-gd php-mbstring php-curl php-zip php-fpm || error_exit "Failed to install PHP 8.3."
}

# Main script
detect_os

case "$OS" in
    debian|ubuntu) 
        install_debian
        ;;
    rhel|centos|fedora)
        install_redhat
        ;;
    *)
        error_exit "Unsupported OS: $OS"
        ;;
esac

# Install Composer

echo "Installing Composer..."
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" || error_exit "Failed to download Composer installer."
php -r "if (hash_file('sha384', 'composer-setup.php') === 'ed0feb545ba87161262f2d45a633e34f591ebb3381f2e0063c345ebea4d228dd0043083717770234ec00c5a9f9593792') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" || error_exit "Failed to verify Composer installer."
php composer-setup.php || error_exit "Failed to install Composer."
php -r "unlink('composer-setup.php');" || error_exit "Failed to clean up Composer installer."

# Update composer.json

echo "Updating composer.json..."
cp composer.json composer.json.bak || error_exit "Failed to create backup of composer.json."

php composer.phar config --no-plugins allow-plugins.yiisoft/yii2-composer true

# Run composer update

echo "Running composer update..."
php composer.phar update || error_exit "Failed to update dependencies."

# Manual steps

echo ""
echo "The project has been upgraded to the latest version of Yii2."
echo "However, you need to manually address the breaking changes in the code."
echo "Please refer to the UPGRADE.md file in the vendor/yiisoft/yii2 directory for a complete list of breaking changes."
echo ""
echo "Here are some of the breaking changes that you need to address:"
echo "- Check all usages of findOne() and findAll() to ensure that input is filtered correctly."
echo "- Updated dependency to cebe/markdown to version 1.2.x."
echo "- Active Record relations are now being reset when corresponding key fields are changed."
echo "- The signature of yii\\helpers\\BaseInflector::transliterate() was changed."
echo "- ...and many more."
echo ""
echo "Please review your application carefully and test it thoroughly before deploying it to production."

exit 0
