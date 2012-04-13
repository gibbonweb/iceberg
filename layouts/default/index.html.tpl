<!DOCTYPE HTML>
    <html>
        <head>
            
            <title>Example Blog</title>
            <link rel="stylehseet" href="<?=$post[0]["data"]["path"]?>/media/css/style.css">
            
        </head>
        <body>
            
            <header>
        		<h1>Welcome to Example Blog!</h1>
            </header>
            
            <ul>
            	<?php foreach ($posts as $post) { ?>
            	<li>
            		<a href="<?=$post["data"]["path"]?>/article/<?=$post["data"]["slug"]?>">
            			<?=$post["data"]["title"]?>
            		</a>
            	</li>
            	<?php } ?>
            </ul>
        
        </body>
    </html>