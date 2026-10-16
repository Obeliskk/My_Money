$ErrorActionPreference = 'Stop'

$php = 'C:\php\php.exe'
$project = Split-Path -Parent $MyInvocation.MyCommand.Path

if (-not (Test-Path (Join-Path $project '.env'))) {
    Copy-Item (Join-Path $project '.env.example') (Join-Path $project '.env')
}

if (-not (Test-Path (Join-Path $project 'database\database.sqlite'))) {
    New-Item -ItemType File -Path (Join-Path $project 'database\database.sqlite') | Out-Null
}

$phpArgs = @(
    '-d', 'extension_dir=C:\php\ext',
    '-d', 'extension=openssl',
    '-d', 'extension=pdo_sqlite',
    '-d', 'extension=sqlite3',
    '-d', 'extension=fileinfo',
    '-d', 'extension=mbstring',
    '-d', 'extension=curl',
    '-d', 'extension=zip',
    '-S', '127.0.0.1:8000',
    '-t', (Join-Path $project 'public')
)
& $php @phpArgs
