Set-Location (Join-Path $PSScriptRoot "..\..")

docker compose run --rm --entrypoint php wpcli /workspace/scripts/import_fabricamos_associados.php /var/www/html/wp-load.php /workspace/data/fabricamos_ifas_planilha_2026-06-05.json --match-dictionary
