<?php
function validateIBAN($iban) {
    // IBAN'ı büyük harfe çevir
    $iban = strtoupper($iban);
    
    // Boşlukları kaldır
    $iban = str_replace(' ', '', $iban);

    // IBAN uzunluklarını ülkelere göre belirle
    $ibanLengths = [
        'TR' => 26, // Türkiye IBAN uzunluğu
        // Diğer ülkelerin IBAN uzunlukları buraya eklenebilir
    ];

    // IBAN'ın ülke kodunu al
    $countryCode = substr($iban, 0, 2);

    // IBAN uzunluğunu kontrol et
    if (!isset($ibanLengths[$countryCode]) || strlen($iban) !== $ibanLengths[$countryCode]) {
        return false;
    }

    // IBAN'ı kontrol basamakları için yeniden düzenle
    $ibanCheck = substr($iban, 4) . substr($iban, 0, 4);

    // Harfleri sayılara dönüştür
    $ibanCheck = str_replace(range('A', 'Z'), range(10, 35), $ibanCheck);

    // IBAN mod 97 kontrolü
    if (bcmod($ibanCheck, '97') != 1) {
        return false;
    }

    return true;
}

function getBankName($iban) {
    // Türkiye için IBAN'dan banka kodunu al
    if (substr($iban, 0, 2) === 'TR') {
        $bankCode = substr($iban, 4, 5);
        $banks = [
            '00001' => 'Ziraat Bankası',
            '00010' => 'Türkiye Cumhuriyeti Merkez Bankası',
            '00111' => 'Finans Bankası',
            '00062' => 'Garanti Bankası',
            '00017' => 'Halk Bankası',
            '00032' => 'VakıfBank',
            '00125' => 'Akbank',
            '00146' => 'İş Bankası',
            '00159' => 'Yapı Kredi Bankası',
            '00205' => 'Garanti Bankası',
            // Diğer bankalar buraya eklenebilir
        ];

        if (isset($banks[$bankCode])) {
            return $banks[$bankCode];
        } else {
            return 'Bilinmeyen Banka';
        }
    }

    return 'Ülke kodu desteklenmiyor';
}

// Kullanıcıdan IBAN al
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $iban = $_POST['iban'];

    // Boşlukları kaldır
    $iban = str_replace(' ', '', $iban);

    if (validateIBAN($iban)) {
        $bankName = getBankName($iban);
        echo "IBAN geçerli. Banka: $bankName";
    } else {
        echo "IBAN geçersiz.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IBAN Doğrulama</title>
</head>
<body>
    <form method="post" action="">
        <label for="iban">IBAN:</label>
        <input type="text" id="iban" name="iban">
        <input type="submit" value="Kontrol Et">
    </form>
</body>
</html>
