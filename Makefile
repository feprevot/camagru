up:
	docker compose up --build -d

down:
	docker compose down

rebuild:
	docker compose down --volumes --remove-orphans
	docker compose up --build -d

logs:
	docker compose logs -f

clean:
	docker compose down -v --remove-orphans
	docker system prune -f

restart: down up

.PHONY: up down rebuild logs clean restart
