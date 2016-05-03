Description:

This script combines files of the same type in a folder into a single compiled file. It looks in a compile_cache folder for the files, and searches for folders listed in its config and creates output files at specififed location.

Required softwares:
	/* PHP5 to execute this script */
	apt-get install php5

	/* Node and Ruby to run JSX and SASS */
	apt-get install nodejs npm ruby

	/* React tools for JSX compile */
	npm install -g react-tools

	/* SASS and globbing for scss compile */
	gem install sass sass-globbing

Sample file structure:
*'bin/js' and 'bin/css' must exist for first compile. With correct permissions compile_cache and subdirectories/files will auto-generate.*
	/working directory i.e. /var/www/html
		/src
			/js
				/app1
				/app2
				...
			/css
				/app1
				/app2
				/app3
		/bin
			/js
			/css
		/compile_cache
			/js
				/.module-cache
				/app1
				/app2
				...
			/css
				/app1
				/app2
				/app3
				...
			compilejsx_hashes.json

In sublime build, use this command to compile your code. Fill out appropriate directories.

	jsx --extension jsx /working directory/src/js /working directory/compile_cache/js && sass -r sass-globbing --no-cache --sourcemap=none --update /working directory/src/css:/working directory/compile_cache/css && php /path to this script/compilejsx.php

Instead of calling with PHP locally, you can also curl call it.
	&& curl http://localhost/.../compilejsx.php
Just ensure the directories are configured on this file.

Modify $arr
	$arr = array(
		"cache_path"	=>	"/working directory/compile_cache",
		"output_path"	=>	"/working directory/bin",
		"jsfolders"		=>	array("app1", "app2", "..."),
		"cssfolders"	=>	array("app1", "app2", "app3", "..."),
		"types"			=>	array("css", "js")
	);

*cache_path* 	- The location of files to be concatenated together
*output_path*	- The location of the concatentated files
*(type)folders*	- The folders of files to be concatenated for the given type
*types*			- File types to be combined. Default css, js

Script will generate a json document at cache_path called "compilejsx_hashes.json". This helps prevent write to disk using a hash check.