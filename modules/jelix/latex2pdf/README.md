This module for Jelix offers a specific response @@C@jResponseLatexToPdf@@. It aims to
convert a [latex](http://fr.wikipedia.org/wiki/LaTeX) document to PDF and send
the result.

These features was in Jelix 1.4 and lower, but have been moved to a new module since Jelix 1.5.


Using latex2pdf
===============

Its alias is "ltx2pdf". So, to retrieve a response:

```php
 $rep = $this->getResponse("ltx2pdf");
```


Note : jResponseLatexToPdf requires `pdflatex` program on the server!

If this program can not be found through a system environment variable (PATH
under linux or Windows), you should assign its full path to `$pdflatexPath`. 

```php
 $resp->pdflatexPath = '/usr/bin/pdflatex';
```

jResponseLatexToPdf takes care of the latex header defining title and authors of
the document. The latex content should be made available through a
template. You should tell jResponseLatexToPdf which template to
use by assigning `$bodyTpl` property with an appropriated selector. You can
pass variable to your template through `$body` property.

More specifically, the `addCommand()` method can generate some latex commands at the beginning of the document.

Example:

```php
   $resp = $this->getResponse("ltx2pdf");

   $resp->title = 'document title';
   $resp->authors[] = 'Michel Dupont';
   $resp->date = '\today'; // default value
   $resp->outputFileName = 'myDocument.pdf';

   $resp->addCommand('documentclass', 'article', array('a4', '14pt'));
   $resp->addCommand('geometry', 'hmargin=1cm, vmargin=2cm');

   $resp->bodyTpl = 'myModule~doclatex';
   $resp->body->assign('texte', $unTexte);

   return $resp;
```
