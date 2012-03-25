<!DOCTYPE HTML>
    <html>
        <head>
            
            <title><?=$post["info"]["title"]?></title>
            <link rel="stylehseet" href="<?=$post["info"]["path"]?>/media/css/style.css">
            
        </head>
        <body>
            
            <header>
                <h1><?=$post["info"]["title"]?></h1>
                <h3>written by <?=$post["info"]["author"]?> on <?=date("F j, Y, g:i a", $post["info"]["time"])?></h3>
            </header>
            <article>
                <?=$post["content"]?>
            </article>
            
        </body>
    </html>