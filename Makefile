up: clean
	docker-compose up -d
dbssh:
	docker-compose exec smserdb /bin/bash
db:
	docker-compose exec smserdb /usr/bin/mysql -u smser -ppassword -h 127.0.0.1 -P 3306 smser
down:
	docker-compose stop
clean:
	docker system prune --force
rm:
	docker-compose stop; docker-compose rm -f
list:
	docker-compose ps
reload: down up
logs:
	docker-compose logs -f
