# PowerShell script: Txt fájlok feldolgozása és MySQL script generálása

# Beállítások
$inputFolder = "C:\tmp\puki"   # Ide írd a mappa elérési útját
$outputFile = "C:\tmp\generated.sql"

# MySQL script inicializálása
$user_id = 1
$sqlLines = @()
$sqlLines += "-- Generált MySQL script"
$sqlLines += "SET @user_id := $user_id;"

# Fájlok bejárása
Get-ChildItem -Path $inputFolder -Filter *.txt | ForEach-Object {
    $file = $_
    $fileName = [System.IO.Path]::GetFileNameWithoutExtension($file.Name)

    # Szótár beszúrása
    $sqlLines += ""
    $sqlLines += "-- Szótár beszúrása: $fileName"
    $sqlLines += "INSERT INTO szotar (megnevezes, user_fk, nyelv1_fk, nyelv2_fk) VALUES ('$fileName', @user_id, 2, 1);"
    $sqlLines += "SET @szotar_id := LAST_INSERT_ID();"

    # Sorok feldolgozása
    Get-Content $file.FullName -Encoding UTF8 | ForEach-Object {
        $line = $_.Trim()
        if ($line -ne "") {
            $parts = $line -split "`t"
            if ($parts.Count -eq 2) {
                $word1 = $parts[0].Replace("'", "''") # SQL escape
                $word2 = $parts[1].Replace("'", "''")

                $guidObj = [System.Guid]::NewGuid()
                $guidBytes = $guidObj.ToByteArray()
                $guidHex = ($guidBytes | ForEach-Object { $_.ToString("X2") }) -join ""

                $sqlLines += "INSERT INTO szo (szo_id, szo, nyelv_fk) VALUES (UNHEX('$guidHex'), '$word1', 2);"
                $sqlLines += "INSERT INTO szo (szo_id, szo, nyelv_fk) VALUES (UNHEX('$guidHex'), '$word2', 1);"
                $sqlLines += "INSERT INTO szotar_szo (szo_fk, szotar_fk) VALUES (UNHEX('$guidHex'), @szotar_id);"

            }
        }
    }
}

# Kiírás fájlba
$sqlLines | Set-Content -Path $outputFile -Encoding UTF8

Write-Host "MySQL script generálva: $outputFile"
