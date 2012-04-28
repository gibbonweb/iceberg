<?php $post = end($posts); ?>
<!DOCTYPE HTML>
    <html>
        <head>
            
            <title><?=$post["data"]["title"]?></title>
            <link rel="stylesheet" href="<?=$post["data"]["path"]?>media/css/style.css">
            
        </head>
        <body>
            
            <header>
                <h1><?=$post["data"]["title"]?></h1>
                <h3>written by <?=$post["data"]["author"]?> on <?=date("F j, Y, g:i a", $post["data"]["time"])?></h3>
            </header>
            <article>
                <?=$post["text"]?>
            </article>
            
        </body>
    </html>