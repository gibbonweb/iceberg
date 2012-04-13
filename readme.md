# Iceberg v1

Iceberg is a static file blogging platform, built around **Markdown**. It runs on PHP 5.3+, and unlike many other alternatives, it doesn't require any external libraries or dependencies. It's as simple download, run the script, and you've got yourself a static blog.
Iceberg is being developed by **Cyril Mengin** (Twitter: [@cyrilmengin](http://twitter.com/cyrilmengin)) as well as various other contributors.

Contents
--------

+ Installing
+ Getting Started
+ Writing Themes
+ Writing Hooks
+ Thanks & Credits
+ License
+ Manual (External File)

Installing
----------

*Iceberg was built on PHP 5.3.8, however, any of the 5.3 updates should be fine.*

Installing Iceberg is very simple, as it doesn't require any specific libraries or extensions, other than the ones that are already installed. Git can be nice to have, but is not required. To install Iceberg, just navigate to wherever you want to install Iceberg, and run the following inside a terminal:

    $ git clone git://github.com/cyrilmengin/iceberg.git TestBlog
    $ cd TestBlog

And, that's it! You should be able to go straight into blogging at this stage, but you might want to mess around with the settings to change the output / source directory first, as well as get a proper layout/ theme.

Getting Started
---------------

So, you've installed Iceberg. That wasn't too hard, but what now? Well, you'll want to create a new post of course!
To do this you will need to create a new directory in your data folder. Note that the directory's name will represent the URL of the post.

    $ cd data
    $ mkdir this-is-my-new-post			# This will later become http://your.blog/articles/this-is-my-new-post

Inside that, you'll want to create your markdown file, as well as an assets folder if you're going to be using images in your post. This can be done in any file browser or editor. Careful, as the name of your markdown file should be the same as the parent directory:

    $ cd this-is-my-new-post
    $ touch this-is-my-new-post.md
    $ mkdir assets

Okay, so that's all the files you'll need to create. If you're using images, stick them all inside the assets directory -- it will later be copied alongside your article. Now, last little bit before you can actually write your post, front-matter! This is just some information that should be placed at the top of the markdown file, in the following format:

    -----
    title: This is my new post!
    author: Cyril Mengin
	slug: this-is-my-new-post		# This is the "slug" URL for your post
    layout: post
    -----

And that's it! You can now write your article underneath that. Once you've finished writing, you're ready to generate the static files. Go back to the root of the iceberg install, and type this:

    $ ./iceberg generate this-is-my-new-post
    -> this-is-my-new-post successfully generated at output/articles/this-is-my-new-post/index.html
    
Your new article should now be awaiting you in the output dir!

Writing Themes
--------------

Writing themes with Iceberg is really easy. It's simply an HTML file containing some PHP. When you set a "layout" file in your post, it will generate the corresponding ``<layout name>.html.tpl`` file (or however you have setup Iceberg to work).
It will be given a single variable; the ``$posts`` variable, which is an array of *all* the posts. The easiest way to get the last post (the one to be compiled) is through the ``end($posts)`` method, which will return the last post array. You can then grab the informations from there.

Another feature of Iceberg is the "reload" file. This is a file named ``<layout name>.reload.yml`` (with the default config). When you run the generate command, before compiling the actual post layout, it will read this file to see if there are any other files that should be reloaded.
If there are some, it will compile them as well, with the new ``$posts`` array. This can be useful, for example, for updating the post list, or an RSS feed.

Writing Hooks
-------------

Iceberg has a hook feature, similar to git. Basically, hooks are scripts that will be run at specific moments during the execution of a command.
Iceberg currently has the following hooks:

+ **preGenerate:** this hook is run before any compiling of posts is done. It will be done for each individual file if the ``--all`` parameter is used.
+ **postGenerate:** this hook is run after any compiling of posts is done. It will be done for each individual file if the ``--all`` parameter is used.

To create a hook, simply create a file in the ``lib/hook`` dir, and put the corresponding code inside. The name of the hook should be ucfirs, and have "Hook" appended to it.
For example:
	
	lib/hook/PostGenerateHook.php
	
	<?php
	
	namespace hook;
	
	use iceberg\hook\AbstractShellHook;
	
	class PostGenerateHook extends AbstractShellHook {
	
		protected static $path = "";
		
		// The command you want to run
		protected static $command = "mkdir example";
		
		// Note that you can also set an array of commands to be run, such as
		//     protected static $command = array("mkdir example", "mkdir example2");
		// These will be run in order
		
		
		public static function prepare() {
			// Anything that should be run before the actual shell hook is run.
			// In this case, we're setting the path where the shell hook should be run.
			// I'm too lazy to try and figure out the relative path, so why not make it absolute?
			static::$path = ROOT_DIR."output";
		}
	
	}
	
or, for a pure-PHP hook;

	lib/hook/PreGenerateHook.php
	
	<?php
	
	namespace hook;
	
	use iceberg\hook\AbstractCodeHook;
	
	class PreGenerateHook extends AbstractCodeHook {
	
		public static function run() {
			echo "The pre-generate hook was run! (Don't worry, it's harmless)", PHP_EOL;
		}
	
	}
	
(Both these examples are actually placeholder hooks, and they're already part of Iceberg, so you can refer to them / edit them right in the hooks directory.)

Thanks & Credits
----------------

**[Michel Fortin](https://github.com/michelf)** for php-markdown, used to  parse Markdown

The **[SPYC Project](http://code.google.com/p/spyc/)** used for parsing YAML

License
-------

Iceberg is licensed under the [WTFPL](http://sam.zoy.org/wtfpl/COPYING) license, so go wild, do what you want.

However, this is not the case of external libraries used in Iceberg, so please see the licenses of PHP-Markdown and SPYC which are located at the top / bottom of the files used.

One thing that should be noted as well (more of a support thing) is that I don't and will not offer support for the installation of iceberg. There will be no backwards compatibilty on this main branch. 

As far as I know, it should work on any PHP 5.3 version (not 5.2, as it requires certain 5.3 specific features), but if it doesn't work, you're on your own. **Of course, I will gladly take bug reports and fix them as long as it is a problem in the code itself. Just make sure the error is replicable in PHP 5.3.8**