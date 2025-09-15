@echo off
echo Importando esquema de base de datos...
railway run -- mysql -h mysql.railway.internal -u root -p%MYSQLPASSWORD% %MYSQLDATABASE% < bd/tiquetera2.sql
echo Schema importado exitosamente!