#!/bin/bash
echo "=== Manual SQL Import ==="

DB_HOST_VAL=${DB_HOST:-${MYSQLHOST:-mysql.railway.internal}}
DB_PORT_VAL=${DB_PORT:-${MYSQLPORT:-3306}}
DB_DATABASE_VAL=${DB_DATABASE:-${MYSQLDATABASE:-railway}}
DB_USERNAME_VAL=${DB_USERNAME:-${MYSQLUSER:-root}}
DB_PASSWORD_VAL=${DB_PASSWORD:-${MYSQLPASSWORD:-}}

echo "Host: ${DB_HOST_VAL}"
echo "Port: ${DB_PORT_VAL}"
echo "Database: ${DB_DATABASE_VAL}"
echo "User: ${DB_USERNAME_VAL}"

if [ -f "database/sql/handyman_service.sql" ]; then
    echo "Importing SQL..."
    export MYSQL_PWD="${DB_PASSWORD_VAL}"
    mysql -h"${DB_HOST_VAL}" -P"${DB_PORT_VAL}" -u"${DB_USERNAME_VAL}" --ssl-mode=DISABLED "${DB_DATABASE_VAL}" < database/sql/handyman_service.sql
    unset MYSQL_PWD
    echo "Done!"
else
    echo "SQL file not found!"
fi
