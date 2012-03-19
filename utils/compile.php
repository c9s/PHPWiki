<?php
$pharFile = 'phpwiki.phar';
$phar = new Phar($pharFile, 0, $pharFile);
$phar->setSignatureAlgorithm(Phar::SHA1);
$phar->startBuffering();
$phar->addFile('php-markdown/markdown.php','markdown.php');
$phar->addFile('src/phpwiki.php','phpwiki.php');

$stub = <<<"EOT"
#!/usr/bin/env php
<?php
Phar::mapPhar('$pharFile');
require 'phar://$pharFile/markdown.php';
require 'phar://$pharFile/phpwiki.php';
__HALT_COMPILER();
EOT;
$phar->setStub($stub);
$phar->stopBuffering();
// $phar->compressFiles(Phar::GZ);
chmod($pharFile , 0777);
echo "Done\n";
