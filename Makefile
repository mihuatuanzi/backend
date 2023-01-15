.PHONY: bash

bash:
	docker run --rm -it -w /var/www/app -v $$(pwd):/var/www/app --user www-data seanscraft/com.mihuatuanzi.backend.app:dev bash
