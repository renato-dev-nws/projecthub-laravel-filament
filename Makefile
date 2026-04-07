SHELL := /bin/bash

.PHONY: testing-db create-testing-db

# Cria o banco de dados de teste no PostgreSQL do container Sail.
# Este alvo usa o serviço pgsql definido em compose.yaml.

testing-db:
	@./vendor/bin/sail exec pgsql bash -lc 'export PGPASSWORD="$$POSTGRES_PASSWORD"; if psql -d postgres -U "$$POSTGRES_USER" -tAc "SELECT 1 FROM pg_database WHERE datname='\''testing'\''" | grep -q 1; then echo "Database testing already exists."; else psql -d postgres -U "$$POSTGRES_USER" -c "CREATE DATABASE testing;" && echo "Database testing created."; fi'

create-testing-db: testing-db
