# FTP adatok
$ftpServer   = "ftp://ftp.nethely.hu"
$ftpUser     = "svckikerdezo"
$ftpPassword = "1DwQZx_ionUM([/*22"

# A mappa, amelyből minden fájlt fel akarunk tölteni
$localFolder = "E:\dev\php\doc-test\php-app"

# Bejárjuk a mappát és az almappákat
$files = Get-ChildItem -Path $localFolder -Recurse -File

foreach ($file in $files) {
    # Az FTP cél elérési útja – megtartjuk a relatív struktúrát
    $relativePath = $file.FullName.Substring($localFolder.Length).TrimStart('\')
    $ftpUri = "$ftpServer/$relativePath" -replace '\\','/'

    Write-Host "Feltöltés: $($file.FullName) → $ftpUri"

    $webClient = New-Object System.Net.WebClient
    $webClient.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPassword)

    try {
        # Feltöltés
        $webClient.UploadFile($ftpUri, $file.FullName)
        Write-Host "Sikeres feltöltés: $relativePath"
    } catch {
        Write-Host "Hiba történt a feltöltésnél: $relativePath - $($_.Exception.Message)"
    }
}


# Fájlok tömbje: régi elérési út + új név
$filesToRename = @(
    @{ OldPath = "ftp://ftp.nethely.hu/inc/config.php"; NewName = "config_old.php" },
    @{ OldPath = "ftp://ftp.nethely.hu/inc/config_tarhelyre.php"; NewName = "config.php" }
    
)

foreach ($item in $filesToRename) {
    $request = [System.Net.FtpWebRequest]::Create($item.OldPath)
    $request.Method = [System.Net.WebRequestMethods+Ftp]::Rename
    $request.Credentials = New-Object System.Net.NetworkCredential($ftpUser, $ftpPassword)
    $request.RenameTo = $item.NewName

    try {
        $response = $request.GetResponse()
        Write-Host "Sikeres átnevezés: $($item.OldPath) → $($item.NewName)"
        $response.Close()
    } catch {
        Write-Host "Hiba történt: $($item.OldPath) - $($_.Exception.Message)"
    }
}