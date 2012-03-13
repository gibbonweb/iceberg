<!DOCTYPE HTML>
    <html>
        <head>
            <title><?=$post["info"]["title"]?></title>
        </head>
        <body>
            
            <h1><?=$post["info"]["title"]?></h1>
            <h3>written by <?=$post["info"]["author"]?> on <?=$post["info"]["time"]?></h3>
            
            <br>
            
            <?=$post["content"]?>
            
        </body>
    </html>