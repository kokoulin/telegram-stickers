<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'Controller.php';
$c = new Controller();


$return = $c->call(
    "{$c->apiUrl}/{$c->botKey}/createnewstickerset",
    [
        'user_id'=>$c->userID,
        'name' => $c->stickerPackName,
        'png_sticker' => $c->mainURL.'stickers/stickers/clear.png',
        'emojis' => '😀',
    ],"GET"
);
echo PHP_EOL.$return.PHP_EOL;