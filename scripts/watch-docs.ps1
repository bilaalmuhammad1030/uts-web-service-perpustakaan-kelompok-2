<#
.SYNOPSIS
    Watcher otomatis untuk DOKUMENTASI.md
.DESCRIPTION
    Memantau perubahan file pada folder app/, routes/, public/, database/, resources/
    dan secara otomatis memperbarui DOKUMENTASI.md tanpa perlu dijalankan manual.
.EXAMPLE
    powershell -ExecutionPolicy Bypass -File .\scripts\watch-docs.ps1
#>

$ErrorActionPreference = 'Continue'
$projectPath = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
Set-Location -LiteralPath $projectPath

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  DOKUMENTASI AUTO-WATCHER PERPUSTAKAAN KELOMPOK 2" -ForegroundColor Green
Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "Memantau perubahan skrip pada proyek: $projectPath"
Write-Host "Setiap ada perubahan skrip (.php, .js, .css, .json), DOKUMENTASI.md akan otomatis terupdate."
Write-Host "Tekan Ctrl+C untuk menghentikan pemantau.`n"

# Jalankan update pertama kali
php scripts/update-docs.php

$watchDirs = @('app', 'routes', 'public', 'database', 'resources')
$watchers = @()

$action = {
    param($source, $eventArgs)
    $ext = [System.IO.Path]::GetExtension($eventArgs.FullPath)
    if ($ext -in @('.php', '.js', '.css', '.json', '.blade.php')) {
        Start-Sleep -Milliseconds 800
        Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Terdeteksi perubahan pada: $($eventArgs.Name)" -ForegroundColor Yellow
        php scripts/update-docs.php
    }
}

foreach ($dir in $watchDirs) {
    $fullDir = Join-Path $projectPath $dir
    if (Test-Path -LiteralPath $fullDir) {
        $fsw = New-Object System.IO.FileSystemWatcher
        $fsw.Path = $fullDir
        $fsw.IncludeSubdirectories = $true
        $fsw.EnableRaisingEvents = $true
        $fsw.NotifyFilter = [System.IO.NotifyFilters]'FileName, LastWrite'

        Register-ObjectEvent $fsw 'Changed' -Action $action | Out-Null
        Register-ObjectEvent $fsw 'Created' -Action $action | Out-Null
        $watchers += $fsw
    }
}

try {
    while ($true) {
        Start-Sleep -Seconds 2
    }
} finally {
    foreach ($w in $watchers) {
        $w.EnableRaisingEvents = $false
        $w.Dispose()
    }
    Get-EventSubscriber | Where-Object { $_.SourceIdentifier -like "*FileSystemWatcher*" } | Unregister-Event
    Write-Host "`nWatcher dihentikan." -ForegroundColor Gray
}
