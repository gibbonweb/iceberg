# Iceberg v1

Iceberg is a static file blogging platform, built around **Markdown** (there is support for Markdown Plus). It runs on PHP 5.3+, and unlike many other alternatives, it doesn't require any external libraries or dependencies. It's as simple download, run the script, and you've got yourself a static blog.
Iceberg is being developed by **Cyril Mengin** (Twitter: [@cyrilmengin](http://twitter.com/cyrilmengin)) as well as various other contributors.

Contents
--------

+ Installing
+ Creating a Post
+ Writing Themes
+ Writing Hooks
+ Thanks & Credits
+ License
+ Manual (External File)

*Note that all examples here are made supposing that you are using the latest version of Iceberg, on the right platform, and that the configuration is the same as the default one (roughly).*

Installing
----------

*Iceberg was built on PHP 5.3.8, however, any of the 5.3 updates should be fine.*

Installing Iceberg is very simple, as it doesn't require any specific libraries or extensions, other than the ones that are already installed. Git can be nice to have, but is not required. If you're worried it might not work, here's a quick list of what's used:

+ Namespaces
+ Output Buffers
+ SQLite3

To install Iceberg, just navigate to wherever you want to install Iceberg, and run the following inside a terminal:

    $ git clone git://github.com/cyrilmengin/iceberg.git TestBlog
    $ cd TestBlog

If you don't have git or don't want to use it, simply use Github's download feature (should be up top).

And, that's it! You should now have a directory with a ``data``, ``lib`` and ``layouts`` directory. You've "installed" Iceberg properly, so read on!

Creating a Post
---------------

So, you've installed Iceberg. That wasn't too hard, but what now? Well, you'll want to create a new post of course!
To do this you will need to create a new directory in your data folder.

To do that, open your favorite text editor, and create a new file with the following:

    -----
    title: This is my new post!
	slug: this-is-my-new-post
    layout: post
    -----
    
    # My New Post
    This post was made using Iceberg and Markdown!

That little chunk at the top of the file is very important. Here's a little description of what the values do:

+ ``title`` : Simple enough, this will just be the title of your post
+ ``author`` : This one isn't on the example, but when enabled, it will override the "global author" which is set in the config.
+ ``slug`` : This will be the url of your post. Let's say your slug is ``this-is-my-new-post``, your post will appear at ``http://example.com/article/this-is-my-new-post``.
+ ``layout`` : This is the name of the template you want to use for this specific post. You can read more about it in the **writting themes** section below.
+ **Others** : Yep, you can also have your custom values in there. Let's say you want to have a custom tag to use in one of your layouts -- you can set it there.

And that's it! You can now write your article underneath that. Once you've finished writing, you're ready to generate the static files. 

First of all, you'll want to save the file as ``<data-dir>/<slug>/<slug>.md``. 
``<data-dir>/`` stands for the directory that contains all the posts. By default, this is the "data" directory in the iceberg root.
``<slug>`` stands for the "slug" name of your post. This should be the same as was set inside the post front-matter.

(If you want to have images, or files attached to your blog post, create an ``assets`` dir inside your article directory, and put them all inside there. Then link to them, *relative* of the post itself, so ``![...](assets/image.png)`` instead of putting a huge path. Iceberg will then expand this into an absolute path.

Now, go back to the root of the iceberg install, and type this:

    $ ./iceberg generate this-is-my-new-post
    -> this-is-my-new-post successfully generated at output/articles/this-is-my-new-post/index.html

Your new article should now be awaiting you in the output dir! However, you'll notice that there isn't any styling, that your blog looks ugly! Well, that's next, so read on once again.

Writing Themes
--------------

Writing themes with Iceberg is really easy. Iceberg uses [Twig](http://twig.sensiolabs.org/) for it's templating, so please refer to it's own documentation for information. Templates should have the ``.twig`` extension.

Another feature of Iceberg is the "reload" file. This is a file named ``<layout name>.reload`` at the same level as the corresponding layout file. When you run the generate command, before compiling the actual post layout, it will read this file to see if there are any other files that should be reloaded.
If there are some, it will compile them as well. This can be useful, for example, for updating the post list on your homepage, or an RSS feed.
This file also supports directories. Let's say you want to copy a directory containing some CSS files, it can do that as well. Here's the general syntax:

	# template: output name
	index: index.html
	rss: feed.rss

	# directory: output path
	static: static

Note that you musn't add the ``.twig`` extension when declaring a template in the reload file.

Writing Hooks
-------------

Iceberg has a hook feature, similar to git. Basically, hooks are scripts that will be run at specific moments during the execution of a command.
They can be useful for (for example) uploading your new blog posts automatically, or compiling LESS / HAML / etc files.

Iceberg currently has the following hooks:

*Note that these hooks will be run for each individual file if the ``--all`` parameter is used. If the --all param finds 3 posts, it will run these hooks 3 times.*

+ **preGenerate:** this hook is run before any compiling of posts is done.
+ **postGenerate:** this hook is run after any compiling of posts is done. 

To create a hook, simply create a file in the ``lib/hook`` directory, and put the corresponding code inside. The name of the hook should be the same as the file name, be ucfirst, and have "Hook" appended to it.
All hooks should extend from either the ``AbstractShellHook`` class, if you want to run a command line script, or the ``AbstractCodeHook`` class, if you want a PHP code hook. For example:
	
	lib/hook/PostGenerateHook.php
	
	<?php
	
	namespace hook;
	
	use iceberg\hook\AbstractShellHook;
	
	class PostGenerateHook extends AbstractShellHook {
	
		/* This is the path in which the hook commands will be run.   */
		/* Iceberg will chdir() into this before running the scripts. */
		/
		protected static $path = "";
		
		/* This is the command you will want to run.                  */
		/* Note that this can also be an array of (strings) commands. */
		protected static $command = "mkdir example";
		
		public static function prepare($posts) {
			/* This function will be called before the actual command is run. */
			/* The $posts param is an array containing all the posts.         */
		}
	
	}

Another example, this time for a code hook:

	lib/hook/PreGenerateHook.php
	
	<?php
	
	namespace hook;
	
	use iceberg\hook\AbstractCodeHook;
	
	class PreGenerateHook extends AbstractCodeHook {
	
		public static function run($posts) {
			/* This function will be called.                          */
			/* The $posts param is an array containing all the posts. */
		}
	
	}

(Both these examples are actually placeholder hooks, and they're already part of Iceberg, so you can refer to them / edit them right in the hooks directory.)

On the other hand, if you'd like to stop any hooks from running during the execution of a command, simply append the ``--no-hook`` parameter to the command (see the manual file for more information).

Thanks & Credits
----------------

**[Michel Fortin](https://github.com/michelf)** for php-markdown, used to  parse Markdown.

The **[SPYC Project](http://code.google.com/p/spyc/)** used for parsing YAML.

**[Twig](http://twig.sensiolabs.org/)** which is being used for templating.

License
-------

Iceberg is licensed under the [WTFPL](http://sam.zoy.org/wtfpl/COPYING) license, so go wild, do what you want.

However, this is not the case of external libraries used in Iceberg, so please see the licenses of PHP-Markdown and SPYC which are located at the top / bottom of the files used.