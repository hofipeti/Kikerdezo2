<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../model/User.php';


// Felhasználó lekérdezése bejelentkezés alapján
function getUserByLogin($username, $password)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user WHERE login = ? AND (password = SHA2(?, 256))");
    $stmt->bind_param("ss", $username, $password);

    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return new User($row['user_id'], $row['login'], $row['nev']);
    }
    return null;
}

function hasSzotar($userId, $szotarNev)
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM szotar WHERE user_fk= ? AND megnevezes = ?");
    $stmt->bind_param("is", $userId, $szotarNev);

    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['count'] > 0;
    }

}

function createSzotar($userId, $szotarNev, $nyelvId1, $nyelvId2)
{
    global $conn;
    $stmt = $conn->prepare("INSERT INTO szotar (user_fk, megnevezes, nyelv1_fk, nyelv2_fk) VALUES (?, ?, ?, ? )");
    $stmt->bind_param("isii", $userId, $szotarNev, $nyelvId1, $nyelvId2);

    return $stmt->execute();
}

function getSzotarByUser($userId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM szotar WHERE user_fk = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $userId);

    $stmt->execute();
    $result = $stmt->get_result();
    $szotarok = [];
    while ($row = $result->fetch_assoc()) {
        $szotarok[] = $row;
    }
    return $szotarok;
}

function getNyelvek()
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM nyelv ORDER BY megnevezes ASC");

    $stmt->execute();
    $result = $stmt->get_result();
    $nyelvek = [];
    while ($row = $result->fetch_assoc()) {
        $nyelvek[] = $row;
    }
    return $nyelvek;
}

function getSzotarById($szotarId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT s.szotar_id, s.megnevezes, n1.nyelv_id nyelv1_id, n1.megnevezes nyelv1, n2.nyelv_id nyelv2_id, n2.megnevezes nyelv2 FROM `szotar` s JOIN nyelv n1 ON s.nyelv1_fk = n1.nyelv_id JOIN nyelv n2 ON s.nyelv2_fk = n2.nyelv_id WHERE s.szotar_id = ?");
    $stmt->bind_param("i", $szotarId);

    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    return null;
}

function getSzo($szo_id) {
    global $conn;
    var_dump($szo_id);
    $stmt = $conn->prepare("SELECT * FROM szo WHERE szo_id = ?");
    $stmt->bind_param("s", $szo_id);

    $stmt->execute();
    
    $result = $stmt->get_result();
    $szavak = [];
    while ($row = $result->fetch_assoc()) {
        $szavak[] = $row;
    }
    return $szavak;
}
function getSzavakBySzotar($szotarId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT s1.szo szo1, s2.szo szo2, ss.szo_fk FROM `szotar` s JOIN szotar_szo ss ON s.szotar_id = ss.szotar_fk JOIN szo s1 ON s1.szo_id = ss.szo_fk AND s1.nyelv_fk = s.nyelv1_fk JOIN szo s2 ON s2.szo_id = ss.szo_fk AND s2.nyelv_fk = s.nyelv2_fk WHERE s.szotar_id = ? ORDER BY ss.created_at DESC");
    $stmt->bind_param("i", $szotarId);

    $stmt->execute();
    $result = $stmt->get_result();
    $szavak = [];
    while ($row = $result->fetch_assoc()) {
        $szavak[] = $row;
    }
    return $szavak;
}

function createSzavak($szotarId, $szo1, $szo2)
{
    global $conn;
    $uuid = generate_uuid_v4();
    $bin = uuid_to_bin($uuid);

    $stmt = $conn->prepare("INSERT INTO szo (szo_id, nyelv_fk, szo) (SELECT ?, s.nyelv1_fk, ?  FROM szotar s  WHERE s.szotar_id = ?)");
    $stmt->bind_param("ssi", $bin, $szo1, $szotarId);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO szo (szo_id, nyelv_fk, szo) (SELECT ?, s.nyelv2_fk, ?  FROM szotar s  WHERE s.szotar_id = ?)");
    $stmt->bind_param("ssi", $bin, $szo2, $szotarId);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO szotar_szo (szo_fk, szotar_fk) VALUES (?, ?)");
    $stmt->bind_param("si", $bin, $szotarId);
    $stmt->execute();


}

function updateSzavak($szo_id,$szotarId, $szo1_val, $szo2_val) {
    global $conn;
    $stmt = $conn->prepare("UPDATE szo SET szo = ? WHERE szo_id = ? AND nyelv_fk = (SELECT s.nyelv1_fk FROM szotar s WHERE s.szotar_id = ?)");
    $stmt->bind_param("ssi", $szo1_val, $szo_id, $szotarId);
    $stmt->execute();   
    $stmt->close();
    
    $stmt = $conn->prepare("UPDATE szo SET szo = ? WHERE szo_id = ? AND nyelv_fk = (SELECT s.nyelv2_fk FROM szotar s WHERE s.szotar_id = ?)");
    $stmt->bind_param("ssi", $szo2_val, $szo_id, $szotarId);
    $stmt->execute(); 
    $stmt->close();
    return true;
}

/**
 * Generate a UUID v4 string (random-based)
 * @return string UUID like 3f9f7c2a-1e4a-4f8a-9c2d-0a1b2c3d4e5f
 */
function generate_uuid_v4(): string
{
    $data = random_bytes(16);
    // set version to 0100
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // set bits 6-7 to 10
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Convert UUID string to 16-byte binary for storing in BINARY(16)
 * @param string $uuid
 * @return string binary(16)
 */
function uuid_to_bin(string $uuid): string
{
    $hex = str_replace('-', '', $uuid);
    return hex2bin($hex);
}

/**
 * Convert 16-byte binary to UUID string
 * @param string $bin
 * @return string
 */
function bin_to_uuid(string $bin): string
{
    $hex = bin2hex($bin);
    return sprintf('%s-%s-%s-%s-%s', substr($hex, 0, 8), substr($hex, 8, 4), substr($hex, 12, 4), substr($hex, 16, 4), substr($hex, 20));
}
function hasAktivFeladat($userId): bool
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM feladat WHERE user_fk= ? AND end_at IS NULL");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['count'] > 0;
    }
    return false;
}

function createFeladat($userId, $szotarIds, $tipus, $szoszamTipus, $szam): bool
{
    global $conn;
    $stmt = $conn->prepare('INSERT INTO feladat (user_fk, feladat_tipus_fk, ismetles) VALUES(?, 1, 3)');
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $newId = $conn->insert_id;
        $stmt->close();
        $placeholders = implode(',', array_fill(0, count($szotarIds), '?'));
        $sql = "INSERT INTO szolista (feladat_fk, szo_fk, nyelv_fk) (SELECT ?, szo_id, nyelv_fk FROM szotar_szo ss
                JOIN szo s ON ss.szo_fk = s.szo_id
                WHERE ss.szotar_fk IN ($placeholders))";

        $stmt = $conn->prepare($sql);
        $params = array_merge([$newId], $szotarIds);
        $types = str_repeat('i', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        if ($stmt->affected_rows > 0)
            return true;

        return false;

    } else {
        return false;
    }
}

function getHatralevokSzama($userId): int
{
    global $conn;
    $stmt = $conn->prepare('
        SELECT SUM(f.ismetles - sl.sikeres) as count FROM szolista sl
        JOIN feladat f ON sl.feladat_fk = f.feladat_id
        WHERE f.user_fk = ? AND f.end_at IS NULL AND sl.sikeres != f.ismetles');
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return (int)$row['count'];
    }
    return 0;
}

function getKerdesekSzama($userId): int
{
    global $conn;
    $stmt = $conn->prepare('
        SELECT SUM(f.ismetles) as count FROM szolista sl
        JOIN feladat f ON sl.feladat_fk = f.feladat_id
        WHERE f.user_fk = ? AND f.end_at IS NULL AND sl.sikeres != f.ismetles');
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return (int)$row['count'];
    }
    return 0;
}

function feladatLezarasa($userId): bool
{
    global $conn;
    $stmt = $conn->prepare('
        UPDATE feladat f SET end_at = IFNULL((SELECT MAX(end_at) FROM kerdes k WHERE k.feladat_fk = f.feladat_id), NOW()) WHERE f.user_fk = ? AND f.end_at IS NULL'
    );
    $stmt->bind_param("i", $userId);
    return $stmt->execute();
}

function createKerdes($userId)
{
    global $conn;
    $hatraVan = getHatralevokSzama($userId);

    if ($hatraVan <= 0) {
        feladatLezarasa($userId);
        return false;
    }
    
    $stmt = $conn->prepare(
        "INSERT INTO kerdes(feladat_fk, szo_fk, nyelv_fk) (SELECT f.feladat_id, s.szo_id, s.nyelv_fk FROM feladat f
                JOIN szolista sl ON sl.feladat_fk = f.feladat_id AND sl.sikeres != f.ismetles
                JOIN szo s ON s.szo_id = sl.szo_fk AND s.nyelv_fk = sl.nyelv_fk
                WHERE f.user_fk = ? AND f.end_at IS NULL
                ORDER BY RAND()
                LIMIT 1)"
    );
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $newId = $conn->insert_id;

    }

    return true;
}

function getKerdes($userId): array
{
    global $conn;
    $stmt = $conn->prepare("SELECT k.kerdes_id, k.feladat_fk, s.* FROM kerdes k JOIN feladat f ON k.feladat_fk = f.feladat_id JOIN szo s ON s.szo_id = k.szo_fk AND s.nyelv_fk = k.nyelv_fk WHERE f.user_fk = ? AND f.end_at IS NULL AND k.end_at IS NULL  ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row;
    } else {
        return [];
    }
}

function createValasz($userId, $valasz): array
{
    global $conn;
    $conn->begin_transaction();

    try {
        $kerdes = getKerdes($userId);
        if (empty($kerdes)) {
            throw new Exception("Nincs aktív kérdés a felhasználó számára.");
        }
        $kerdesId = $kerdes['kerdes_id'] ?? null;
        $stmt = $conn->prepare("
            UPDATE kerdes k
            JOIN szo s ON k.szo_fk = s.szo_id AND k.nyelv_fk != s.nyelv_fk
            SET
                k.helyes = CASE WHEN ? = s.szo THEN 1 ELSE 0 END,
                k.end_at = NOW(),
                valasz = ?
            WHERE k.kerdes_id = ?;");
        $stmt->bind_param("ssi", $valasz, $valasz, $kerdesId);
        $stmt->execute();
        $conn->commit();
        $result = $stmt->affected_rows;
        $stmt->close();

        $stmt = $conn->prepare("SELECT helyes FROM kerdes WHERE kerdes_id = ?");
        $stmt->bind_param("i", $kerdesId);
        $stmt->execute();
        $res = $stmt->get_result();
        $helyes = 0;

        if ($row = $res->fetch_assoc()) {
            $helyes = $row['helyes'];
        }
        $stmt->close();

        if ($helyes === 1) {

            $sql = "UPDATE szolista SET  sikeres = sikeres+1, probalkozas = probalkozas + 1 WHERE feladat_fk =? AND szo_fk = ? AND nyelv_fk = ? ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $kerdes['feladat_fk'], $kerdes['szo_id'], $kerdes['nyelv_fk']);
            $stmt->execute();

        } else {
            $stmt = $conn->prepare("UPDATE szolista SET sikeres=0, probalkozas = probalkozas + 1  WHERE feladat_fk =? AND szo_fk = ? AND nyelv_fk = ? ");
            $stmt->bind_param("isi", $kerdes['feladat_fk'], $kerdes['szo_id'], $kerdes['nyelv_fk']);
            $stmt->execute();

        }
        $stmt->close();

        $conn->commit();
        return ['success' => true, 'helyes' => $helyes];

    } catch (Exception $e) {
        $conn->rollback();
        die("Hiba történt: " . $e->getMessage() . "\n" . $e->getTraceAsString());

    }


}

function getEredmenyek($userId): array
{
    global $conn;
    $stmt = $conn->prepare("
        SELECT *, TIME_TO_SEC(TIMEDIFF(end_at, start_at)) AS diff_seconds  FROM feladat f
        JOIN (SELECT feladat_fk, SUM(sikeres) sSikeres, SUM(probalkozas) sProbalkozas FROM szolista sl GROUP BY sl.feladat_fk ) s ON s.feladat_fk = f.feladat_id
        WHERE f.user_fk = ? ORDER BY f.start_at DESC"
        
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $eredmenyek = [];
    while ($row = $result->fetch_assoc()) {
        $eredmenyek[] = $row;
    }
    return $eredmenyek;
}

function getUtolsoFeladat(int $userId): array  {
    global $conn;
    $stmt = $conn->prepare("
        SELECT end_at FROM feladat f
        WHERE f.user_fk = ? AND f.end_at IS NOT NULL ORDER BY f.end_at DESC LIMIT 1"
        
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    return [];

}