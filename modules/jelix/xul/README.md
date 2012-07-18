
This a a module for a Jelix application, which brings components to support of XUL:
response objects, template plugins, javascript helpers etc..

XUL support was provided natively in Jelix 1.4 and lower, but is becommed an
independant module since Jelix 1.5.

XUL is a user interface description language. It is useful to develop rich web
applications in the browser space, without dealing with complex javascript
and dom manipulations. See [xul](http://developer.mozilla.org/en/XUL). 

Note: Only Gecko-based browser renders XUL (Firefox is the most famous one),
and you have to install the [Remote XUL manager](https://addons.mozilla.org/fr/firefox/addon/remote-xul-manager/) addon,
to allow to use XUL in a web application.


jResponseXul and friends
========================

The base class for XUL response is jResponseXul. There are also derived class for specific UI elements.
List of XUL responses:   
  * jResponseXul generates a XUL window (<window>). 
  * jResponseXulDialog is for dialogs (<dialog>). 
  * jResponseXulOverlay for XUL overlays (<overlay>)
  * jResponseXulPage is for XUL window included in another XUL via <iframe> 

As jResponseHtml, jResponseXul generates its main tag but also links
to stylesheets, javascripts scripts and overlays (static or dynamic).


Generating XUL
==============

Here is the different XUL response aliases :

```php
  $window = $this->getResponse("xul");
  $dialog = $this->getResponse("xuldialog");
  $overlay = $this->getResponse("xuloverlay");
  $page = $this->getResponse("xulpage");
```

Below, we'll use `$window` but all examples work with other responses except contrary indications.

You can add attribute to XUL response main tag (<window>, <overlay>, <dialog> or <page>):

```php
  $window->rootAttributes = array("width"=>"300");
```

Linking stylsheets, scripts or overlays to XUL response is done through calls to
methods `addCssLink()`, `addJsLink()` and `addOverlay()`:

```php
  $window->addCssLink('style.css');
  $window->addJsLink('scripts.js');
  $window->addOverlay('overlay.xul');
```


Generating without template
===========================

If you don't want to use a template, you'll have to call `addContent` method
to populate your XUL response.

```php
   $rep->addContent('xul content');
```



Generating with a template
==========================

`$body` member property defines a jTpl object. `$bodyTpl` should be assigned the
template selector to use. And `$title` is to set the window title.

```php
 $window->title = "my super title";
 $window->bodyTpl = "mymodule~xullist";
 $window->body->assign("list", $list);
```

As jResponseHtml, you can include content before or after template content. Use
`addContent()` method. It takes a content string as first argument and a
boolean (optional) to indicate wether the content should be generated before
(true) or (after, default value) template content.

```php
$rep->addContent('XUL after template');
$rep->addContent('XUL before template', true);
```

You can also use zones for content.


Overlays
========

An overlay is a XUL file. It is useful to modify a XUL page without modifying
its source. Firefox extensions use overlays extensively. It can be also useful
in a web application and Jelix of course. A module could overload a XUL page of
another module using an overlay.

The use is truely simple, it is based on jelix events system. jResponseXul and
its derived class, emits an event before sending content. Modules can catch this
event and respond to it with an overlay url. if so, a `<?xul-overlay?>` tag
will be added to the response. 

The action using jResponseXul has to set `$fetchOverlays` to true:

```php
  $window->fetchOverlays = true;
```


if `$fetchOverlays` is not set, no event will be emitted and thus no extern
overlays will be included in the response.


Use case
========

Create a `jResponseXulOverlay` response. Say it is action "testa~xul:overlay1"
and "testb~xul:index" is the action displaying a XUL page where the overlay
should apply.

In "testa" module, an event listener should respond to "FetchXulOverlay" event
emitted by jResponseXul used in module "testb" action xul:index. In a file,
clases/testa.listener.php, you should have:

```php
class testaListener extends jEventListener{

   function onFetchXulOverlay($event){

   }
}
```


`FetchXulOverlay` event as a parameter "tpl", which value is the template
selector used by "testb~xul:index" action. Imagine this selector is
"testb~mainxul". If this template corresponds to your overlay, you will respond
to the event indicating an overlay url. 

```php
class testaListener extends jEventListener{

   function onFetchXulOverlay($event){
      if($event->getParam('tpl') == 'testb~mainxul'){
            $event->Add('testa~xul:overlay1');
        }
   }
}
```

Do not forget to declare this listener in events.xml file of "testa" module:

```xml
<events xmlns="http://jelix.org/ns/events/1.0">
   <listener name="testa">
       <event name="FetchXulOverlay" />
   </listener>
</events>
```

There you are, overlay1 of module "testa" will be loaded with page xul:index of
module "testb".

