#!/usr/bin/env bash

rsync -av --chown=webapp:webapp --force --delete --progress \
	--exclude 'vendor' \
	--exclude 'node_modules' \
	--exclude 'storage' \
	--exclude 'public/storage' \
	--exclude 'public/dist' \
	--exclude '.env' \
	../webapp/ root@machine.food-at-home.lsferreira.net:/srv/http/webapp/webapp/
rsync -av --chown=webapp:webapp --force --delete --progress \
	--exclude 'node_modules' \
	../websockets/ root@machine.food-at-home.lsferreira.net:/srv/http/webapp/websockets/
