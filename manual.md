# ``$ ./iceberg generate <article-name>``

This command will generate a the post who's name was passed as argument. The input and output paths are set in the config file.
For example, running ``./iceberg generate hello-world`` would generate ``<article-name>/hello-world/hello-world.md``.

# ``$ ./iceberg generate --all``

This parameter replaces the article name parameter, and when run, it will run the generate function on all posts that are found in the post data directory.
*Any hooks that would usually be triggered during the process with the plain command will be triggered for every post: if 3 posts are generated, the hooks will be run 3 times*