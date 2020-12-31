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


all: up

start: DOCKER_OPTIONS=--detach
start: up

stop: down

up:
	@echo "--> Starting docker environment..."
	@export CURRENT_UID=$(shell stat -c "%u:%g" ./webapp/); docker-compose up $(DOCKER_OPTIONS)

down:
	@echo "--> Stop docker environment..."
	@export CURRENT_UID=$(shell stat -c "%u:%g" ./webapp/); docker-compose down $(DOCKER_OPTIONS)

restart:
	@echo "--> Restarting docker environment..."
	@export CURRENT_UID=$(shell stat -c "%u:%g" ./webapp/); docker-compose restart $(DOCKER_OPTIONS)

logging:
	@docker-compose logs -f --tail=1000

build:
	@echo "--> Building dockers in parallel..."
	@docker-compose build --parallel

watch-frontend:
	@echo "Watching frontend webpack resources..."
	@sh -c "(cd webapp; npm run watch)"

install-all:
	make install-bootstrap-webapp
	make install-websockets
	make install-composer

install-bootstrap-webapp:
	@echo "Installing webapp dependencies..."
	@cd webapp; npm install

install-websockets:
	@echo "Installing websockets dependencies..."
	@cd websockets; npm install

install-composer:
	@echo "Installing composer dependencies..."
	@cd webapp; composer install

status:
	@docker-compose ps --all

clean:
	@echo "--> Removing database files..."
	@rm -rf .php-devenv/
