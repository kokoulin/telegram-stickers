<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'Controller.php';
$c = new Controller();

$stickers = [
    'MyFirstSticker'=>[
        'emoji'=>['😀'],
        'filename'=>'mfs',
    ],
];

foreach ($stickers as $sticker) {
    try {
        $newImg = new Imagick();
        $anim = new Imagick();
        $newImg->readImage(__DIR__ . "/template/clear.png");
        $draw1 = new ImagickDraw();
        $pixel = new ImagickPixel('transparent');

        //Дата//
        $draw1 = setSetting($draw1, [
            'color' => 'white',
            'opacity' => 0.9,
            'fontSize' => 30,
        ]);
        $newImg->annotateImage($draw1, 20, 10, 0, date('d.m.Y H:i'));

        $anim->addImage($newImg);
        $rnd = rand(0, 999999);
        $name = __DIR__ . "/stickers/sticker_{$rnd}.png";
        $anim->writeImages($name, true);

        $c = new Controller();
        $return = $c->call(
            "{$c->apiUrl}/{$c->botKey}/getStickerSet",
            [
                'name' => $c->stickerPackName,
            ], "POST"
        );
        $return = json_decode($return, 1);

        $stickerIDs =[];
        foreach ($return['result']['stickers'] as $item) {
            if (in_array($item['emoji'], $sticker['emoji'])) {
                $stickerIDs[] = $item['file_id'];
            }
        }

        if (sizeof($stickerIDs) > 0) {
            foreach ($stickerIDs as $stickerID) {
                $returnDel = $c->call(
                    "{$c->apiUrl}/{$c->botKey}/deleteStickerFromSet",
                    [
                        'sticker' => $stickerID,
                    ], "POST"
                );
            }
        }
//        echo PHP_EOL;
//        var_dump($returnDel);
//        echo PHP_EOL;

        $return = $c->call(
            "{$c->apiUrl}/{$c->botKey}/addstickertoset",
            [
                'user_id' => $c->userID,
                'name' => $c->stickerPackName,
                'png_sticker' => "{$c->mainURL}/stickers/stickers/{$sticker['filename']}_{$rnd}.png",
                'emojis' => $sticker['emoji'][0],
            ], "POST"
        );
        echo PHP_EOL . $return . PHP_EOL;

    } catch (Exception $e) {
        echo "<pre>-----------------------------\n";
        echo "Error code " . $e->getCode() . ": " . $e->getMessage() . ' in line [' . $e->getLine() . '] in file [' . $e->getFile() . ']' . "\n";
        echo $e->getTraceAsString();
        if (isset($return)) {
            var_dump($return);
        } else {
            echo 'nodata' . PHP_EOL;
        }
        echo "\n-----------------------------\n";
    }
}
function setSetting(ImagickDraw $draw1,array $setting){
    $draw1->setFillColor($setting['color']);
    $draw1->setFillOpacity($setting['opacity']);
    $draw1->setFontSize($setting['fontSize']);
    $draw1->setFont($setting['font']);
    $draw1->setTextAlignment(Imagick::ALIGN_CENTER);
    $draw1->setGravity(Imagick::GRAVITY_NORTHWEST);
    $draw1->setTextAlignment(Imagick::ALIGN_LEFT);
    return $draw1;
}



