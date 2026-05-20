# Local Dev

Este repositório nao inclui o nucleo do WordPress. Para rodar localmente, use Docker.

## Requisitos

- Docker Desktop
- PowerShell

## Subir ambiente

```powershell
cd C:\Programação\plataforma-fabricamos\plataforma-fabricamos
Copy-Item .env.example .env
.\scripts\local\bootstrap-local.ps1
```

Para subir ja importando os fabricantes/substancias:

```powershell
.\scripts\local\bootstrap-local.ps1 -ImportData
```

## Restaurar o dump real

Se voce tiver um dump do banco do Fabricamos, use este fluxo em vez do install limpo:

```powershell
.\scripts\local\restore-dump-local.ps1 -DumpPath "C:\caminho\Dump.sql"
```

Esse restore:
- recria o banco local
- importa o dump
- usa o prefixo real `oyqm_`
- fixa `home` e `siteurl` para `http://localhost:8090`
- cria/atualiza o usuario admin local configurado no `.env`

Observacao:
- o repositÃ³rio nao inclui plugins premium como `elementor-pro`, `jet-engine`, `jet-elements` e `jupiterx-core`
- portanto o dump restaura o banco real, mas o visual so fica 100% igual se esses plugins e os uploads tambem existirem localmente

## Enderecos

- Site: `http://localhost:8090`
- Admin: `http://localhost:8090/wp-admin`
- phpMyAdmin: `http://localhost:8091`

Credenciais padrao do admin local:

- usuario: `admin`
- senha: `admin123456`

## Reimportar substancias

```powershell
.\scripts\local\import-local.ps1
```

## Derrubar ambiente

```powershell
docker compose down
```

Para apagar banco e arquivos do WordPress local:

```powershell
docker compose down -v
```
