<?php
function getRelativePath($from, $to)
{
   $from = explode('/', $from);
   $to = explode('/', $to);
   foreach($from as $depth => $dir)
   {

        if(isset($to[$depth]))
        {
            if($dir === $to[$depth])
            {
               unset($to[$depth]);
               unset($from[$depth]);
            }
            else
            {
               break;
            }
        }
    }
    //$rawresult = implode('/', $to);
    for($i=0;$i<count($from)-1;$i++)
    {
        array_unshift($to,'..');
    }
    $result = implode('/', $to);
    return $result;
}

function PhpMarkdown($text)
{
    // replace php tags with highlight string
    $text = preg_replace('#(^<\?.*?\?>)#sme', 
        '"\n\n<pre class=\"php\">" . str_replace("\n","",highlight_string(str_replace(\'\\"\',\'"\',\'$1\'),true)) . "</pre>\n\n" ', 
        $text );
    return Markdown($text);
}


//Default style.
$style=<<<EOF
    body { 
        padding: 15px 30px; 
        background-color: #f3f3f3;
        font-family: sans-serif;
    }
    a { color: blue; }
    h1, h2, h3, h4 { 
        color: #232323;
        text-shadow: #ddd 1px 1px 1px;
    }
    p { 
        color: #333;
        text-shadow: #ccc 1px 1px 1px;
    }
    pre { 
        margin: 10px 15px;
        background: #eee;
        border-radius: 10px;
        border: 1px solid #999;
        -moz-box-shadow:    inset 0 0 10px #000000;
        -webkit-box-shadow: inset 0 0 10px #000000;
        box-shadow:         inset 0 0 10px #000000;
        padding: 10px;
    }
    code { 
        font-family: Monaco, Monospace, Courier New;
        font-size: 0.8em;
    }
    table { background: #aaa; }
    table td { 
        background: #eee; 
        color: #111;
        padding: 3px 6px;
        min-width: 200px;
    }
EOF;

if( empty($argv) || count($argv) < 3 ) {
    die("Usage: phpwiki [input] [output] [-s, --style [<value>]]\n");
}

use GetOptionKit\GetOptionKit;

list($script,$input,$output) = $argv;

// $output = 'wiki_html';
if( ! file_exists($output) )
    mkdir( $output , 0755 , true );

$stylesheet = null;
$opt = new GetOptionKit;
$opt->add( 's|style?' , 'option with another stylesheet file' );

// $opt->specs->printOptions();
try {
    $result = $opt->parse( $argv );
    if(isset($result->style))
        $stylesheet = $result->style;
} catch( Exception $e ) {
    die( $e->getMessage() );
}

//if stylesheet exists
if( file_exists($stylesheet) ) {
    $style = file_get_contents($stylesheet);
}

$dirs = array($input);

$wrapper =<<<HTML
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
    %STYLE%
    </style>
</head>
<body>
%BODY%
</body>
</html>
HTML;

echo "Generating wiki doc to $output ...\n";
 
foreach( $dirs as $dir ) {

    foreach (new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir), 
            RecursiveIteratorIterator::LEAVES_ONLY) as $file)
    {
        $ext = $file->getExtension();
        if( $ext == 'mkd' || $ext == 'md' ) {


            $filedir = $file->getPath();
            $htmlFilename = $file->getBasename( '.'. $ext ) . '.html';
            $subdir = dirname( substr( $file->getPathname() , strlen($dir) + 1 ) );
            if( '.' == $subdir )  { 
                $path = $output . DIRECTORY_SEPARATOR . $htmlFilename;
            } else { 
                $path = $output . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR . $htmlFilename;
            }

            $text = file_get_contents($file);

            // re-filter wiki links
            $text = preg_replace('#\[\[(.*?)\|(.*?)\]\]#xe',
                " '<a href=\"' . getRelativePath('$path','$output/$1.html') . '\">' . '$2' . '</a>' ",
                $text);

            $text = preg_replace('#\[\[(.*?)\]\]#xe',
                " '<a href=\"' . getRelativePath('$path','$output/$1.html') . '\">' . '$1' . '</a>' ",
                $text);

            $html = PhpMarkdown($text);
            $html = str_replace('%BODY%', $html, $wrapper );
            $html = str_replace('%STYLE%', $style, $html );//replace style area
            $d = dirname($path);
            if( ! file_exists($d) ) 
                mkdir( $d , 0755, true );

            echo "\t" , $path , "\n";
            file_put_contents( $path , $html );
        }
    }
}
echo "Done", "\n";
