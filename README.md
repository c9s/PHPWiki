PHPWikiGen
==========

phpwiki.phar is a small, standalone executable that helps you convert markdown wiki into html pages,
and supports php built-in highlight method.

* small.
* fast.
* support wiki link format.
* use php built-in highlight method.

Screenshot
----------

<img src="http://cloud.github.com/downloads/c9s/PHPWikiGen/Screen%20Shot%202012-03-19%20at%20%E4%B8%8B%E5%8D%8812.59.32.png"/>

Installation
------------

Simply use wget;

    $ wget https://raw.github.com/c9s/PHPWiki/master/phpwiki

Usage
-----
Convert markdown wiki to html

    php phpwiki wiki html

View Html For PHP5.4:

    cd html
    php -S localhost:8888


Hacking
-------

1. Get PHP Onion:

2. Run onion bundle command:

    $ onion install

3. run compile command:

    $ bash utils/compile.sh

