# Pour démarrer un serveur de développement rapidement sans apache
.PHONY: serve
serve:
	php -S localhost:8080 -t public -f index.php

.PHONY: compose
compose:
	docker-compose down && docker-compose up -d