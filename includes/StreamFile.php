<?php

function wfStreamFile( $fname ) {
	global $wgSquidMaxage;
	$stat = stat( $fname );
	if ( !$stat ) {
		header( 'HTTP/1.0 404 Not Found' );
		echo "<html><body>
<h1>File not found</h1>
<p>Although this PHP script ({$_SERVER['SCRIPT_NAME']}) exists, the file requested for output 
does not.</p>
</body></html>";
		return;
	}

	header( "Cache-Control: s-maxage=$wgSquidMaxage, must-revalidate, max-age=0" );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $stat['mtime'] ) . ' GMT' );

	if ( !empty( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) {
		$sinceTime = strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
		if ( $stat['mtime'] <= $sinceTime ) {
			header( "HTTP/1.0 304 Not Modified" );
			return;
		}
	}
	
	$type = wfGetType( $fname );
	if ( $type ) {
		header("Content-type: $type");
	} else {
		header('Content-type: application/x-wiki');
	}
	readfile( $fname );
}

function wfGetType( $filename ) {
	# There's probably a better way to do this
	$types = <<<END_STRING
application/andrew-inset ez
application/mac-binhex40 hqx
application/mac-compactpro cpt
application/mathml+xml mathml
application/msword doc
application/octet-stream bin dms lha lzh exe class so dll
application/oda oda
application/ogg ogg
application/pdf pdf
application/postscript ai eps ps
application/rdf+xml rdf
application/smil smi smil
application/srgs gram
application/srgs+xml grxml
application/vnd.mif mif
application/vnd.ms-excel xls
application/vnd.ms-powerpoint ppt
application/vnd.wap.wbxml wbxml
application/vnd.wap.wmlc wmlc
application/vnd.wap.wmlscriptc wmlsc
application/voicexml+xml vxml
application/x-bcpio bcpio
application/x-cdlink vcd
application/x-chess-pgn pgn
application/x-cpio cpio
application/x-csh csh
application/x-director dcr dir dxr
application/x-dvi dvi
application/x-futuresplash spl
application/x-gtar gtar
application/x-hdf hdf
application/x-javascript js
application/x-koan skp skd skt skm
application/x-latex latex
application/x-netcdf nc cdf
application/x-sh sh
application/x-shar shar
application/x-shockwave-flash swf
application/x-stuffit sit
application/x-sv4cpio sv4cpio
application/x-sv4crc sv4crc
application/x-tar tar
application/x-tcl tcl
application/x-tex tex
application/x-texinfo texinfo texi
application/x-troff t tr roff
application/x-troff-man man
application/x-troff-me me
application/x-troff-ms ms
application/x-ustar ustar
application/x-wais-source src
application/xhtml+xml xhtml xht
application/xslt+xml xslt
application/xml xml xsl
application/xml-dtd dtd
application/zip zip
audio/basic au snd
audio/midi mid midi kar
audio/mpeg mpga mp2 mp3
audio/x-aiff aif aiff aifc
audio/x-mpegurl m3u
audio/x-pn-realaudio ram rm
audio/x-pn-realaudio-plugin rpm
audio/x-realaudio ra
audio/x-wav wav
chemical/x-pdb pdb
chemical/x-xyz xyz
image/bmp bmp
image/cgm cgm
image/gif gif
image/ief ief
image/jpeg jpeg jpg jpe
image/png png
image/svg+xml svg
image/tiff tiff tif
image/vnd.djvu djvu djv
image/vnd.wap.wbmp wbmp
image/x-cmu-raster ras
image/x-icon ico
image/x-portable-anymap pnm
image/x-portable-bitmap pbm
image/x-portable-graymap pgm
image/x-portable-pixmap ppm
image/x-rgb rgb
image/x-xbitmap xbm
image/x-xpixmap xpm
image/x-xwindowdump xwd
model/iges igs iges
model/mesh msh mesh silo
model/vrml wrl vrml
text/calendar ics ifb
text/css css
text/richtext rtx
text/rtf rtf
text/sgml sgml sgm
text/tab-separated-values tsv
text/vnd.wap.wml wml
text/vnd.wap.wmlscript wmls
text/x-setext etx
video/mpeg mpeg mpg mpe
video/quicktime qt mov
video/vnd.mpegurl mxu
video/x-msvideo avi
video/x-sgi-movie movie
x-conference/x-cooltalk ice
END_STRING;
	// Needed for windows servers who use \r\n not \n
	$endl = "
";
	$types = explode( $endl, $types );
	if ( !preg_match( "/\.([^.]*?)$/", $filename, $matches ) ) {
		return false;
	}

	foreach( $types as $type ) {
		$extensions = explode( " ", $type );
		for ( $i=1; $i<count( $extensions ); $i++ ) {
			if ( $extensions[$i] == $matches[1] ) {
				return $extensions[0];
			}
		}
	}
	return false;
}

?>
