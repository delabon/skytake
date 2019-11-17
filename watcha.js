var fs = require('fs');
var watch = require('node-watch');
var cssmin = require('cssmin');
var jsmin = require('jsmin').jsmin;
var settings = require('./watcha.settings');
const exec = require('child_process').exec;

settings = settings.settings;

if( ! settings.hasOwnProperty('minify') ){
	settings.minify = false;
}

/**
 * check if file exists
 * @param  {string}  file_path
 */
function isFileExists( file_path ){
	if( ! fs.existsSync( file_path ) ){
		console.error( 'File does not exists: ' + file_path );
		process.exit();
	}
}

/**
 * Read files content
 * @return string
 */
function readFilesContent( files ){
	var content = '';
	
	// Read file in synchronously (blocking)
	for( var i=0; i < files.length; i++ ){
		content += fs.readFileSync( __dirname + files[i], 'utf8') + "\r\n";
	};

	return content;
}

/**
 * Add Content To File
 * @param array files
 * @param string final_file
 * @param string type
 * @param bool minify
 */
function addContentToFile( files, final_file, type, minify ){	
	var content = readFilesContent( files );

	if( type == 'css' && minify ){
		content = cssmin( content );
	}

	if( type == 'js' && minify ){
		content = jsmin( content, 3 );
	}

	fs.writeFileSync( __dirname + final_file, content, 'utf8');
}

/**
 * Watching for changes
 * @param array files
 * @param string final_file
 * @param string type
 * @param bool minify
 */
function startWatching( files, final_file, type, minify ){

	console.log( "["+type.toUpperCase()+"]: " );

	for( var i=0; i < files.length; i++ ){
		var currentFile = __dirname + files[ i ];

		isFileExists( currentFile );
		console.log( currentFile );

		watch( currentFile, function( event, name ){
			if( event == 'update'){
				addContentToFile( files, final_file, type, minify );
				console.log('changed: '+name);
			}
		});
	}

	console.log( "--> " + final_file );
}

// watch for javascript files changes
if( settings.hasOwnProperty('js')){
	startWatching( 
		settings.js.files, 
		settings.js.dump_into, 
		'js', 
		settings.minify 
	);
}

// watch for css files changes
if( settings.hasOwnProperty('css')){
	startWatching( 
		settings.css.files, 
		settings.css.dump_into, 
		'css', 
		settings.minify 
	);
}

// watch for sass changes
if( settings.hasOwnProperty('sass') ){
	var str = 'sass --watch ' + __dirname + settings.sass[0] + ':' + __dirname + settings.sass[1];
		
	isFileExists( __dirname + settings.sass[0] );
	isFileExists( __dirname + settings.sass[1] );

	if( settings.minify ){
		str += " --style compressed";
	}

	code = exec( str );
	console.log( "[SASS]: " );
	console.log( __dirname + settings.sass[0] );
	console.log( "--> " + __dirname + settings.sass[1] );
}
