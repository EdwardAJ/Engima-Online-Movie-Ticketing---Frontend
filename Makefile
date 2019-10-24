#MAKEFILE

include .env
export

db-migrate:
	@echo "Migrating database..."
	@sudo docker exec -i ${MYSQL_CONTAINER_NAME} mysql -u ${MYSQL_USER} -p${MYSQL_PASS} -e 'USE ${MYSQL_DATABASE};$(shell cat migrate/create_tables.sql)'
	@echo "Migration finished."

db-drop:
	@echo "Dropping database..."
	@sudo docker exec -i ${MYSQL_CONTAINER_NAME} mysql -u ${MYSQL_USER} -p${MYSQL_PASS} -e 'USE ${MYSQL_DATABASE};$(shell cat migrate/drop_tables.sql)'
	@echo "Drop finished."