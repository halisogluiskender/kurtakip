<?php
include("fonksiyon.php");
// Altın döviz Takibi
$url_doviz = "https://canlidoviz.com/doviz-kurlari/kapali-carsi";
$url_altin = "https://canlidoviz.com/altin-fiyatlari/kapali-carsi";
// Mahalle Nüfus Hesaplama
$content = [];
$content['döviz'] = curl_canliDoviz($url_doviz);
$content['altın'] = curl_canliDoviz($url_altin);

// Kullanımı
$newTable = convertToNewTable($content, ['USD', 'EUR', 'ATA', '22', 'C', 'XHGLD', 'Y', 'GA']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
</style>

<body>
    <table border style="width: 100%;">
        <thead>
            <tr>
                <th>DÖVIZ</th>
                <th>ALIS FİYATI</th>
                <th>SATIS FİYATI</th>
                <th style="width:200px;">ELİMDE OLAN</th>
                <th style="width:200px;">EDERİ</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($newTable); $i++) { ?>
                <tr>
                    <!-- <?php for ($j = 0; $j < count($newTable[$i]); $j++) { ?>
                        <td style="text-align: center;"><?php echo $newTable[$i][array_keys($newTable[$i])[$j]]; ?> <input type="hidden" name="<?php echo array_keys($newTable[$i])[$j] ?>" value="<?php echo $newTable[$i][array_keys($newTable[$i])[$j]]; ?>"></td>
                    <?php } ?> -->
                    <td style="text-align: center;"><?php echo $newTable[$i]["currency"]; ?></td>
                    <td style="text-align: center;"><?php echo $newTable[$i]["buy_price"]; ?> <input type="hidden" name="buy_price" value="<?php echo $newTable[$i]["buy_price"]; ?>"></td>
                    <td style="text-align: center;"><?php echo $newTable[$i]["sell_price"]; ?> <input type="hidden" name="sell_price" value="<?php echo $newTable[$i]["sell_price"]; ?>"></td>
                    <td>
                        <div style="display:flex;">
                            <input style="width: 50%; margin:0;box-sizing:border-box;" type="number" oninput="hesaplaBizimSatis(this)" placeholder="Satis">
                            <input style="width: 50%; margin:0;box-sizing:border-box;" type="number" oninput="hesaplaBizimAlis(this)" placeholder="Alis">
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;">
                            <div style="width:50%;">
                                <span style="width:100%; display:block;text-align:center;">Satış</span>
                                <div class="satis" style="width: 100%; text-align:center; font-size:14px;"></div>
                            </div>
                            <div style="width:50%;">
                                <span style="width:100%; display:block;text-align:center;">Alış</span>
                                <div class="alis" style="width: 100%; text-align:center; font-size:14px;"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="genelToplam" style="padding-top:15px;width:100%;display:grid;align-items:center;justify-content:center; grid-template-columns: repeat(2, 1fr);">
        <div style="text-align:center; font-weight:bold; ">Toplam Satış</div>
        <div style="text-align:center; font-weight:bold; ">Toplam Alış</div>
        <div class="toplamSatis" style="text-align:center; font-weight:bold;"></div>
        <div class="toplamAlis" style="text-align:center; font-weight:bold;"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script>
        function genelToplam() {
            var tr = $("table tbody tr");
            var toplamSatis = 0;
            var toplamAlis = 0;
            tr.each(function() {
                var satis = parseFloat($(this).find("td:last").find(".satis").text().replace(" TL", "").replace(",", "."));
                var alis = parseFloat($(this).find("td:last").find(".alis").text().replace(" TL", "").replace(",", "."));

                if (!isNaN(satis)) {
                    toplamSatis += satis;
                }
                if (!isNaN(alis)) {
                    toplamAlis += alis;
                }
            })
            $('.genelToplam').find(".toplamSatis").text(toplamSatis.toFixed(3) + " TL");
            $('.genelToplam').find(".toplamAlis").text(toplamAlis.toFixed(3) + " TL");
        }

        function hesaplaBizimSatis(e) {
            var onValue = parseFloat($(e).val());

            var inputs = $(e).closest("tr").find("input[type=hidden]");

            inputs.each(function() {
                if ($(this).attr("name") == "buy_price") {
                    var alis_fiyati = $(this).val();
                    var lastTD = $(e).closest("tr").find("td:last");
                    var sonuc = onValue * alis_fiyati;
                    var formattedNumber = sonuc.toLocaleString('tr-TR', {
                        currency: 'TRY'
                    });
                    if (formattedNumber !== 0 && formattedNumber !== null && formattedNumber !== undefined && formattedNumber !== "NaN") {
                        lastTD.find('.satis').text(formattedNumber + " TL");
                    } else {
                        lastTD.find('.satis').text("");
                    }
                }
            });
            genelToplam();
        }

        function hesaplaBizimAlis(e) {
            var onValue = parseFloat($(e).val());

            var inputs = $(e).closest("tr").find("input[type=hidden]");

            inputs.each(function() {
                if ($(this).attr("name") == "sell_price") {
                    var alis_fiyati = $(this).val();
                    var lastTD = $(e).closest("tr").find("td:last");
                    var sonuc = onValue * alis_fiyati;
                    var formattedNumber = sonuc.toLocaleString('tr-TR', {
                        currency: 'TRY'
                    });
                    if (formattedNumber !== 0 && formattedNumber !== null && formattedNumber !== undefined && formattedNumber !== "NaN") {
                        lastTD.find('.alis').text(formattedNumber + " TL");
                    } else {
                        lastTD.find('.alis').text("");
                    }
                }
            });
            genelToplam();
        }

        // AJAX isteği gönderme fonksiyonu
        function ajaxIstegiGonder() {
            $.ajax({
                url: '', // AJAX isteği gönderilecek PHP dosyasının yolu
                type: 'POST', // POST metodu kullanılacak
                data: {
                    action: 'get_data'
                }, // İstek verisi, gerekirse isteğe özel parametreler de ekleyebilirsiniz
                success: function(response) {
                    // AJAX isteği başarılı olduğunda yapılacak işlemler
                    console.log('AJAX isteği başarılı:', response);
                    // Sonuçları uygun şekilde işleyin
                    // Örneğin, sonuçları bir div içine yerleştirin
                    $('#result').html(response);
                },
                error: function(xhr, status, error) {
                    // AJAX isteği başarısız olduğunda yapılacak işlemler
                    console.error('AJAX isteği başarısız:', error);
                }
            });
        }

        // Belirli aralıklarla AJAX isteği gönderme
        // setInterval(ajaxIstegiGonder, 10000); // 10 saniyede bir istek gönder
    </script>
</body>

</html>