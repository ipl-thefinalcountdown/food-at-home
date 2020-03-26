#                                     __
#                                    / _|
#   __ _ _   _ _ __ ___  _ __ __ _  | |_ ___  ___ ___
#  / _` | | | | '__/ _ \| '__/ _` | |  _/ _ \/ __/ __|
# | (_| | |_| | | | (_) | | | (_| | | || (_) \__ \__ \
#  \__,_|\__,_|_|  \___/|_|  \__,_| |_| \___/|___/___/
#
# Copyright (C) 2020 Aurora Free Open Source Software.
# Copyright (C) 2020 Luís Ferreira <luis@aurorafoss.org>
#
# This file is part of the Aurora Free Open Source Software. This
# organization promote free and open source software that you can
# redistribute and/or modify under the terms of the Boost Software License
# Version 1.0 available in the package root path as 'LICENSE' file. Please
# review the following information to ensure that the license requirements
# meet the opensource guidelines at opensource.org .
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE, TITLE AND NON-INFRINGEMENT. IN NO EVENT
# SHALL THE COPYRIGHT HOLDERS OR ANYONE DISTRIBUTING THE SOFTWARE BE LIABLE
# FOR ANY DAMAGES OR OTHER LIABILITY, WHETHER IN CONTRACT, TORT OR OTHERWISE,
# ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
# DEALINGS IN THE SOFTWARE.
#
# NOTE: All products, services or anything associated to trademarks and
# service marks used or referenced on this file are the property of their
# respective companies/owners or its subsidiaries. Other names and brands
# may be claimed as the property of others.
#
# For more info about intellectual property visit: aurorafoss.org or
# directly send an email to: contact (at) aurorafoss.org .

FROM php:7.4-fpm-alpine
LABEL maintainer="Aurora Free Open Source Software <contact at aurorafoss.org>"

# Add xdebug php extension

# Add required depeendencies for running
RUN apk --update --no-cache add \
		freetype \
		libpng \
		libjpeg-turbo \
		libzip \
		oniguruma \
		libxml2 \
		openssl-dev \
		libmcrypt \
		composer

# Add required dependencies for building
RUN apk --update --no-cache --virtual .build-deps add \
		$PHPIZE_DEPS \
		freetype-dev \
		libjpeg-turbo-dev \
		libmcrypt-dev \
		libpng-dev \
		libzip-dev \
		oniguruma-dev \
		libxml2-dev

# Install pecl extensions and enable it
RUN pecl install \
		mcrypt-1.0.3 \
		xdebug-2.8.1 \
	&& docker-php-ext-enable \
		xdebug \
		mcrypt

# Configure php extensions
RUN docker-php-ext-configure gd \
		--with-freetype=/usr/include \
		--with-jpeg=/usr/include

# Install php docker extensions
RUN docker-php-ext-install -j$(nproc) \
		iconv \
		bcmath \
		pdo \
		pdo_mysql \
		mysqli \
		opcache \
		zip \
		mbstring \
		soap \
		ctype \
		tokenizer \
		xml \
		json \
		gd

# Delete build dependencies
RUN apk del -f .build-deps

# Delete cache
RUN rm -rf /tmp/* /var/cache/apk/*
