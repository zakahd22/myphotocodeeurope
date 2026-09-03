#!/bin/bash

# Obtenemos el total de archivos
total_archivos=$(find /kunden/homepages/46/d399659235/htdocs/ | wc -l)

# Si el total de archivos no se puede obtener, salimos con un error
if [ -z "$total_archivos" ]; then
  echo "No se pudo obtener el total de archivos."
  exit 1
fi

# Si el total de archivos es superior a 200000, ejecutamos el script y salimos
if [ "$total_archivos" -gt 200000 ]; then
  echo "El total de archivos es superior a 200000. Ejecutando el script..."
  php5 /kunden/homepages/46/d399659235/htdocs/common/script/script_mypc_CheckSpace.php
  exit 0
else
  echo "El total de archivos es inferior a 230000. No se ejecuta el script."
fi

# Salimos del script
exit 0
