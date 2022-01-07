#!/usr/bin/env bash
sudo chmod -R o+w storage; php artisan config:clear; php artisan config:cache;php artisan config:clear;php artisan cache:clear;php artisan view:clear;php artisan route:clear