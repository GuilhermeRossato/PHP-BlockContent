#PHP-FlexPart

FlexPart is a lightweight PHP Framework without dependencies that works by assuming websites are made out of nested blocks. It helps development by making websites easily scalable, efficiently cacheable (fast), extremely dynamic, flexible, reliable and easy to debug.

There will be a repository to hold a fully operational website built with this framework, but currently it is not operational.

#Blocks

Each block is a html element that can have other blocks inside, as well as content. Each block has a XML configuration file which describes its behaviours and optionally it's style.

For example, a block on a page can be defined by the following configuration file:

````xml
<block>
	<tag>div</tag> <!-- Optional, but that's the default value -->
	<name>index</name>
	<content>
		<block>menu</block>
		<block>body</block>
		<block>footer</block>
	</content>
</block>
````

The description above describes `div` block that instances 3 other blocks as its content, each of the instanced blocks will have their own configuration file. This block can be instanced in PHP by the following snippet:

````php
<?php
	require_once __DIR__ . "/FlexPart/include.php"; // An include file that includes the framework
	$config_dir = __DIR__ . "/FlexPart/config/";
	$cache_dir = __DIR__ . "/FlexPart/cache/";
	$log_file = __DIR__ . "/FlexPart/log.txt"; // Optional log file
	$app = new \Rossato\FlexPart($config_dir, $cache_dir, $log_file);
	echo $app->getPageFromBlock("index");
?>
````

Note that all nested blocks will also be interpreted and shown in the resulting file.

If we didn't use any styling in the blocks of our website, we could just use `$app->asHTML("index")` to get the block as HTML.

#Block tags and names 

Each block is by default a <div> element. This can be reconfigured by the `tag` in the configuration, like so: `<tag>input</tag>`.

Block names are unique identifiers for blocks of a website and are used to instanciate a block.
If a block name is not specified, the filename (without the extension) will be used instead.
They are composed of letters, numbers, dots and slashes. Anything else will throw an InvalidBlockNameException during the parsing of the blocks.

If you specify styling in the configuration file, the classname of the block will be it's name (unless you overwrite this by configurating a specific classname).

Having the same name and classname is useful for debugging: If you need to edit a block, be it's structure or style, its classname will lead you to the file of the block.

#Block Content

Blocks can have either other blocks, raw text, associated files or all of these at the same time:

````xml
<block>
	<name>block</name>
	<content>
		<raw>Hello world!<raw> <!-- This is mostly for tests and debugging only -->
		<file>/../random-external-folder/index.php</file> <!-- There will be no caching for the 'index.php' file -->
		<block>another-block</block>
	</content>
</block>
````

Results into a content of:

````html
<div class="block">Hello world!
{{content of index.php file}}
<div class="another-block">content of another block</div>
</div>
````

The newlines were added for conveniance, they are not included in the real output.

#Style

The CSS namespace has a tendency to become polluted as projects grow and stylesheets can go up to thousands of lines in a project.

This framework introduces an **optional** way of organizing style that aims to move styling out of a general file and onto these configuration files, the framework then condenses this styling into a single file and is able to do css minifying and other cache-tricks to fasten the loading of a webpage.

A good grasp of CSS is advised to anyone using this framework, as it can be very useful.

##Conditional Styling

Suppose you have a webpage that must behave differently according to the device width:

````xml
<block><name>hello-world</name>
	<style>
		<default>
			display: inline-block;
			position: fixed;
			width: 100px;
		</default>
		<desktop>
			display: block;
			width: auto;
			top: 0;
		</desktop>
		<tablet>
			top: 50%;
			left: 0;
		</tablet>
		<mobile>
			bottom: 0;
		</mobile>
	</style>
</block>
````

If we abstract the css compression that the framework does by default, we would be generating the following style file:

````css
/* Others blocks default definitions goes here */
.hello-world {
	display: inline-block;
	position: fixed;
	width: 100px;
}
@media screen and (min-width: 900px) {
	/* Others blocks desktop definitions goes here */
	.hello-world {
		display: block;
		width: auto;
		top: 0;
	}
}
@media screen and (min-width: 500px) and (max-width: 900px) {
	/* Others blocks tablet definitions goes here */
	.hello-world {
		top: 50%;
		left: 0;
	}
}
@media screen and (max-width: 500px) {
	.hello-world {
		bottom: 0;
	}
}
````

It's also possible to configure raw style to be freely added onto the style file, for when you need to use selectors or any other new CSS specification unhandled by the framework:

````xml
<block>
	<name>my-block</name>
	<style>
		<default>
			display: flex;
		</default>
		<desktop>width: 400px;</desktop>
		<tablet>width: 200px</mobile>
		<raw>
			.my-block::after:hover {
				content: "Click me!";
			}
		</raw>
	</style>
</block>
````

Alternatively, if you would rather create your own style file and manually put on the page, you must assign classes and/or ids to a block at its configuration file:

<style>
	<name>my-block</name>
	<class>my-class my-other-class</class>
	<id>hello_world</id>
</style>

You must specify a class name because the block name is only used as the classname if you specify something in the `<style>` tag.

##Cache Invalidation

Everytime the style of a block is modified in a configuration file the cache files must be purged. This can be done manually by deleting the cache folder or programatically by the use of the `app->purgeCache()` method.

There is **no** automatic cache purging, the style is solid once it is generated and **MUST** be purged when necessary.

You can put the `purgeCache` method on your main index.php file during development to always purge the styling, BUT YOU MUST **NOT FORGET** THIS ON, as it will heavily influence website performance.

#Inspiration

I first say a need for this type of framework when I was working as a Magento developer. Magento is an E-commerce solution in which one of its aspect is a system that enables pages to be built in blocks, each calling other child blocks.

However, I wanted to extract that usefulnes and extend that into something simpler, more abstract and easier to use.

#License

Still working on it.