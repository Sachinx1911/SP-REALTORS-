#!/bin/sh
# PHP syntax check for plugin + themes (dev tool).
# चालवणे:  docker run --rm -v "$PWD:/app" -w /app php:7.4-cli sh docker/tools/lint.sh
#          (PHP 8.x साठी image बदला: wordpress:php8.2-apache)

status=0
for dir in sp-realtors-core sp-realtors sp-realtors-child docker; do
	[ -d "$dir" ] || continue
	for file in $(find "$dir" -name "*.php"); do
		if ! php -l "$file" > /dev/null 2>&1; then
			php -l "$file"
			status=1
		fi
	done
done

php -v | head -n 1
if [ "$status" -eq 0 ]; then
	echo "LINT OK"
else
	echo "LINT FAILED"
fi
exit "$status"
