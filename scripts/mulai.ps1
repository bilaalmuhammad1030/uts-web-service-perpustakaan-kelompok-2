param([string]$MySqlBinary = 'C:/RDMMadrasah/mysql/bin/mysqld.exe')
$ErrorActionPreference = 'Stop'
$projectPath = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$dataPath = [System.IO.Path]::GetFullPath((Join-Path $projectPath '../.local-mysql'))
Set-Location -LiteralPath $projectPath
if (-not (Test-Path -LiteralPath '.env')) { throw 'Salin .env.example menjadi .env dan ikuti README terlebih dahulu.' }
$usesLocalDatabase = (Get-Content .env | Where-Object { $_ -eq 'DB_PORT=3307' })
if ($usesLocalDatabase) {
    $tcp = New-Object System.Net.Sockets.TcpClient
    try { $tcp.Connect('127.0.0.1',3307); $isRunning = $true } catch { $isRunning = $false } finally { $tcp.Dispose() }
    if (-not $isRunning) {
        if (-not (Test-Path -LiteralPath $dataPath) -or -not (Test-Path -LiteralPath $MySqlBinary)) {
            throw 'MariaDB lokal belum tersedia. Ikuti bagian database pada README.'
        }
        $basePath = Split-Path (Split-Path $MySqlBinary -Parent) -Parent
        Start-Process -FilePath $MySqlBinary -ArgumentList @('--no-defaults', ('--basedir="' + $basePath + '"'), ('--datadir="' + $dataPath + '"'), '--port=3307','--bind-address=127.0.0.1','--console') -WindowStyle Hidden -RedirectStandardOutput (Join-Path $dataPath 'server-output.log') -RedirectStandardError (Join-Path $dataPath 'server-error.log')
        Start-Sleep -Seconds 3
    }
}
Write-Host 'Buka katalog: http://127.0.0.1:8000'
Write-Host 'Buka admin: http://127.0.0.1:8000/admin'
Write-Host 'Tekan Ctrl+C untuk menghentikan server Laravel.'
php artisan serve --host=127.0.0.1 --port=8000 --no-reload
