# What is AJAX? #

For those who do not know; AJAX (Asynchronous JavaScript and XML) is a method of updating specific content on an HTML page with out sending back or redrawing the entire page. AJAX is very generic. You can replace any content on the page and it can be triggered from any JavaScript callback.

# AJAX in Phaux is easy #

Phaux makes asynchronous updates of the rendered html page easy. The content you want to replace is drawn using a typical Phaux render method and the update is triggered in much that same way that a standard Phaux callback is triggered.


### Live Update Method ###

There are two live update methods. These methods can be called on any tag in your render methods in your component. liveUpdateOn on does not perform a callback and simply relaces content on the page and liveUpdateWithCallbackOn takes both a callback and a render method.

```

$html->anchor()->with('Click Me')->
    liveUpdateWithCallbackOn("onClick",
        $this,"renderMessageOn",array(),
        $this,"setMessage",array($message));
```

The above code created an anchor with the label 'Click Me' and associates a render method and a callback with the "onClick" event of that anchor. Lets break it down...

```
liveUpdateWithCallbackOn("onClick",
```
Set the live update with the "onClick" event. This can be any supported JavaScript like "onMouseOver", "onMouseOut", "onFocus", "onBlur", etc..

```
$this,"renderMessageOn",array(),
```
Sets renderMessageOn on $this with no extra arguments to be the render method of this live update. The third argument must be an array and can contain any extra arguments you might want to pass to the render method. The first argument passed to the render method will be an instance of WHHtmlCanvas your extra arguments will be passed as the 2nd, 3rd, etc. arguments.

```
$this,"setMessage",array($message));
```
Registers a callback on this live update. This callback works just like a standard Phaux callback except you must specify an array of arguments (it can be empty).

### Render Method ###

The method for rendering is just like a regular render method and can (usually is) called from your renderContentOn() method. There is only one rule that your render method that is used for a live update must follow. It must return an element with a **unique** id as it's first tag. This element's content is then used to replace the content of element with the same id on the current page.

The render method for the above example might look like...
public function renderMessageOn($html){
    return $html->div()->id("message")->with($this->message);
}```