param(
    [Parameter(Mandatory = $true)]
    [string]$DumpPath,
    [switch]$KeepVolumes
)

$ErrorActionPreference = "Stop"

Set-Location (Join-Path $PSScriptRoot "..\..")

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "Arquivo .env criado a partir de .env.example"
}

$envMap = @{}
foreach ($line in Get-Content ".env") {
    if ($line -match "^\s*#" -or $line -notmatch "=") {
        continue
    }

    $parts = $line -split "=", 2
    $envMap[$parts[0]] = $parts[1]
}

$resolvedDump = (Resolve-Path $DumpPath).Path
$mysqlDatabase = $envMap["MYSQL_DATABASE"]
$mysqlRootPassword = $envMap["MYSQL_ROOT_PASSWORD"]
$wpUrl = $envMap["WORDPRESS_URL"]
$wpAdminUser = $envMap["WORDPRESS_ADMIN_USER"]
$wpAdminPassword = $envMap["WORDPRESS_ADMIN_PASSWORD"]
$wpAdminEmail = $envMap["WORDPRESS_ADMIN_EMAIL"]

if (-not $KeepVolumes) {
    docker compose down -v
} else {
    docker compose down
}

docker compose up -d db wordpress

Write-Host "Aguardando MySQL responder..."
Start-Sleep -Seconds 20

$dbContainerId = (docker compose ps -q db).Trim()
if (-not $dbContainerId) {
    throw "Nao foi possivel localizar o container do banco."
}

docker cp $resolvedDump "${dbContainerId}:/tmp/fabricamos-dump.sql"
docker compose exec -T db sh -lc 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < /tmp/fabricamos-dump.sql'

Write-Host "Aguardando WordPress responder..."
Start-Sleep -Seconds 10

docker compose run --rm wpcli core is-installed

docker compose run --rm wpcli option update home "$wpUrl"
docker compose run --rm wpcli option update siteurl "$wpUrl"
docker compose run --rm wpcli rewrite flush --hard

cmd /c "docker compose run --rm wpcli user get $wpAdminUser >nul 2>nul"
if ($LASTEXITCODE -ne 0) {
    docker compose run --rm wpcli user create $wpAdminUser $wpAdminEmail --role=administrator --user_pass=$wpAdminPassword
} else {
    docker compose run --rm wpcli user update $wpAdminUser --user_pass=$wpAdminPassword --user_email=$wpAdminEmail
}

Write-Host ""
Write-Host "Dump restaurado no ambiente local."
Write-Host "Site: $wpUrl"
Write-Host "Admin: $($wpUrl.TrimEnd('/'))/wp-admin"
Write-Host "Usuario local: $wpAdminUser"
Write-Host "Senha local: $wpAdminPassword"
