param(
    [switch]$ImportData
)

$ErrorActionPreference = "Stop"

Set-Location (Join-Path $PSScriptRoot "..\..")

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "Arquivo .env criado a partir de .env.example"
}

docker compose up -d db wordpress

Write-Host "Aguardando WordPress responder..."
Start-Sleep -Seconds 15

$envFile = Get-Content ".env"
$envMap = @{}
foreach ($line in $envFile) {
    if ($line -match "^\s*#" -or $line -notmatch "=") {
        continue
    }
    $parts = $line -split "=", 2
    $envMap[$parts[0]] = $parts[1]
}

$wpUrl = $envMap["WORDPRESS_URL"]
$wpTitle = $envMap["WORDPRESS_TITLE"]
$wpAdminUser = $envMap["WORDPRESS_ADMIN_USER"]
$wpAdminPassword = $envMap["WORDPRESS_ADMIN_PASSWORD"]
$wpAdminEmail = $envMap["WORDPRESS_ADMIN_EMAIL"]

docker compose run --rm wpcli core is-installed 2>$null
if ($LASTEXITCODE -ne 0) {
    docker compose run --rm wpcli core install `
        --url="$wpUrl" `
        --title="$wpTitle" `
        --admin_user="$wpAdminUser" `
        --admin_password="$wpAdminPassword" `
        --admin_email="$wpAdminEmail" `
        --skip-email
}

docker compose run --rm wpcli theme activate jupiterx
docker compose run --rm wpcli plugin activate fabricamos-native

if ($ImportData) {
    docker compose run --rm --entrypoint php wpcli /workspace/scripts/import_fabricamos_associados.php /var/www/html/wp-load.php /workspace/data/fabricamos_ifas_planilha_2026-06-05.json --match-dictionary
}

Write-Host ""
Write-Host "WordPress local pronto:"
Write-Host "Site: $wpUrl"
Write-Host "Admin: $($wpUrl.TrimEnd('/'))/wp-admin"
Write-Host "Usuario: $wpAdminUser"
Write-Host "Senha: $wpAdminPassword"
