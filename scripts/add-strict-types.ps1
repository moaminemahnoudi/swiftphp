# PowerShell script to add declare(strict_types=1) to all PHP files

Get-ChildItem -Path src -Recurse -Filter *.php | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    
    if ($content -notmatch 'declare\(strict_types=1\)') {
        $lines = Get-Content $_.FullName
        $newContent = @()
        
        # Add first line (<?php)
        $newContent += $lines[0]
        $newContent += ""
        $newContent += "declare(strict_types=1);"
        
        # Add rest of the file (skip first line)
        for ($i = 1; $i -lt $lines.Count; $i++) {
            $newContent += $lines[$i]
        }
        
        $newContent | Set-Content $_.FullName
        Write-Host "Added strict_types to: $($_.FullName)"
    }
}

Write-Host "Done!"
