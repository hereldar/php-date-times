fix := false

ecs:
ifeq ($(fix), true)
	vendor/bin/ecs --fix --ansi
else
	vendor/bin/ecs --ansi
endif

phpstan:
	vendor/bin/phpstan --ansi

phpunit:
	vendor/bin/phpunit

psalm:
	vendor/bin/psalm --threads=4 --diff

coding-standards: ecs
static-analysis: phpstan psalm
tests: phpunit
