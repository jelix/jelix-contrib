This module provides RDF support: response object, request object...
RDF features was natively included into Jelix 1.4 and lower, but has been moved
into this external module since Jelix 1.5.


jResponseRdf generates an RDF formatted document. There is a real world use case
for it :-). It is useful for XUL application and specifically for XUL templates.
the response alias is `rdf`.

There is two ways to use jResponseRdf:
- either you fill it with a datalist and it generates corresponding RDF.
- either you use a template. It is necessary for some complex RDF graphs.


Automatic RDF generation
========================

It does work only for RDF representing a data list (not trees for example).

You have to assign `$datas` member properties with a data list. It can a list
of associative arrays, a jResultSet object (an iterator) returned from
`jDb::query` or from a dao select method:

```php
 $rep = $this->getResponse("rdf");

 $dao = jDao::get('users');
 $rep->datas = $dao->findAll();
```

Also:

```php
 $rep->datas = array(
         array('name'='washington', 'firstname'=>'georges'),
         array('name'=>'churchill', 'firstname'=>'winston'),
         array('name'=>'jaurès', 'firstname'=>'jean'),
   );
```

The latter will generate:

```xml
<RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:row="http://dummy/rdf#">

   <Bag RDF:about="urn:data:row">
      <li><Description>
              <row:nom>washington</row:nom>
              <row:prenom>georges</row:prenom>
           </Description></li>
      <li><Description>
              <row:nom>churchill</row:nom>
              <row:prenom>winston</row:prenom>
           </Description></li>
      <li><Description>
              <row:nom>jaurès</row:nom>
              <row:prenom>jean</row:prenom>
           </Description></li>
   </Bag>
</RDF>
```

jResponseRdf declares as default namespace "http://dummy/rdf#" with "row"
prefix. this prefix is used for data elements. You can choose your own namespace
by settting `$resNs` and `$resNsPrefix` properties respectively.

```php
  $rep->resNs = 'http://mysupersite.com/ns/users/';
  $rep->resNsPrefix = 'user';
```

You can also modify the data list id which defaults to "urn:data:row":

```php
  $rep->resUriRoot = 'urn:mysupersite:users';
```

You might also want to choose selectively between generating data informations
as attributes or as elements. You'll have to declare elements and attributes
through `$asElement` and `$asAttribute` properties.

```php
  $rep->asAttribute = array('firstname');
  $rep->asElement = array('name');
```

Note : as soon as you set one of those two properties, every data entity in your
data list must appear in one of those arrays in order to be generated in the RDF
output. 

Example:

```xml
<RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:user="http://mysupersite.com/ns/users/">

   <Bag RDF:about="urn:mysupersite:users">
      <li><Description user:firstname="georges">
              <user:name>washington</user:name>
           </Description></li>
      <li><Description user:firstname="winston">
              <user:name>churchill</user:name>
           </Description></li>
      <li><Description user:firstname="jean">
              <user:name>jaurès</user:name>
           </Description></li>
   </Bag>
</RDF>
```



Generating RDF with template
============================

Alike other responses, you can use templates to generate RDF with jResponseRdf.
You have to use `$datas` member to pass datas to the template. You can then
use `$datas` in your template as you like. As for example, it is common to
iterate over it. See example below. You also have to indicate which template to
use through `$template` properties.

Example:
```php
 $rep = $this->getResponse("rdf");

 $rep->datas = array(
         array('nom'=>'dupont', 'prenom'=>'georges'),
         array('nom'=>'durant', 'prenom'=>'paul'),
         array('nom'=>'duchemin', 'prenom'=>'jacques'),
   );
 $rep->template = 'mymodule~datasrdf';
```

And datasrdf.tpl:

```xml
<RDF xmlns:RDF="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:user="http://mysupersite.com/ns/users/">

   <Bag RDF:about="urn:mysupersite:users">

   {foreach $datas as $dt}
      <li><Description user:firstname="{$data['firstname']|escxml}">
              <user:name>{$data['name']|escxml}</user:name>
           </Description></li>
   {/foreach}

   </Bag>
</RDF>
```

Note: you don't have to generate xml prolog (<?xml... )

Note: `$resNs`, `$resNsPrefix`, `$resUriRoot`, `$asElement` and
`$asAttribute` have no effects if you use a template.

