# Faking it with Phaux #
This tutorial should serve as in introduction to Phaux. To get started either
download Phaux from [the phaux homepage](http://code.google.com/p/phaux/) or
get the latest version from the [subversion repository](http://code.google.com/p/phaux/source).

### Getting Phaux ###
You should either untar the download or move the Phaux source tree somewhere
in your Apache DocRoot path. Phaux also requires at least PHP 5.2.Installing
and configuring Apache or PHP5 is beyond the scope of this tutorial.

You should now be able to access Phaux applications using the following URL...

```
http://<server ip or hostname/<path to phaux HtmlRoot>/phaux.php/<application name>
```
For example if you installed Phaux under Apache's DocRoot on your local host
and you wanted to access the counter application your URL might look like ...

```
http://localhost/Phaux/HtmlRoot/phaux.php/counter
```
The rest of this tutorial assumes that Phaux is installed in the DocRoot and
the above URL can be used to access the counter application. Of course in the
future you may want to change Apache's DocRoot in order to accommodate shorter
URLs.

### Basics: Sessions and Components ###
Access http://localhost/Phaux/HtmlRoot/phaux.php/counter to bring up the
counter application. The counter is a very simple application that displays a
number and allows you to increase or decrease that number. Go ahead and click
the "++" and "--" links.

Take a look at the URL. You will notice two URL args one named SID (we will deal
with the other later) but no URL arguments that specifies the value of the
number you see displayed on the page. Phaux does the work of maintaining
state, in this case the value of the number, for you transparently. Phaux is
able to do that by keeping track of who you are with a unique session id, SID.

There is another URL argument that you might not see, _k._k is the way Phaux
knows what action the User wants to perform.

Click on "Toggle Halos", it's on the development toolbar at the bottom of your
page. Halos allow you to gather more information about that components that
are drawn on the page. You will now see a halo around the counter component
(WHCounter). Click on the "H" on the far right of the halo. You should now be
able to see the HTML out put of the WHCounter. Notice that the links for "++"
and "--" have an URL argument "_k" associated with them._

The reason you don't normally see the _k in your location bar is because any
time after an action is preformed Phaux redirects you to a new url without an
action. This prevents side affect actions like the user clicking the back
button or the reload button and the action happening again. Go ahead and click
"R" where "H" use to be. This should bring the component back to being
rendered on the page._

The other URL argument, _r, is the unique registry key. The registry is a way
to store things that might be specific to the current view. That is it can
store things that do follow the back and forward buttons of your users
browser. It allows Phaux to do things like navigation._

Phaux applications work as a series of interacting components. When an
application is first accessed a new session is created and the main component
is created. The main component then displays its self and waits for user
input. User input (a clicked link or a submitted form) will trigger a
callback, then the component will display its self again. These callbacks can
do anything you wish, including updating the current component or displaying
another component.


### The Makings of A Phaux Component ###
A Phaux component is a sub class of WHComponent and at a minimum tells Phaux
how to render its self by implementing renderContentOn().

Open up the file Phaux/Phaux-test/WHCounter.php in your favorite text editor.
This the the class for the counter application. You will may notice that there
appears to be nothing out of the ordinary with this class. It defines an
instance variable, $counter and a couple methods.

Lets look at the renderContentOn method.

```
public function renderContentOn($html){
	return $html->div()->id("counter")->with(
		$html->headingLevel(1)->with(sprintf("%s",$this->counter)).
		$html->anchor()->callback($this,"add")->with("++").
		$html->text(" ").
		$html->anchor()->callback($this,"subtract")->with("--")
	);
}
```

renderContentOn() tells Phaux how the component should be rendered. It does
that by returning an Instance of WHHtmlCanvas or more accurately returning an
object that can be converted to a string or a string. renderContentOn gets
passed one variable, by convention named $html, that is an instance of
WHHtmlCanvas.

Lets dissect the above method.

```
$html->div()->id("counter")
```

Creates a new div tag with the attribute id='counter'. Other attributes can be
added to div by chaining them after div().

```
$html->div()->id("counter")->class('foo-bar')
```
The above would create a div that would look like <div id='counter'
class='foo-bar'>

Unknown end tag for &lt;/div&gt;



You will notice that after we set the id of the div we call with(). With
accepts one argument, the content we would like to see inside the tag. Most
tags implement with().

with() takes a string or an object that can be converted into a string like an
instance of WHHtmlCanvas. It is recommended you use the WHHtmlCanvas instance
($html) when ever you want to output HTML.

```
$html->headingLevel(1)->with(sprintf("%s",$this->counter)).
```

The above creates a heading of level 1 with the value of the counter cased to
a string as its content. There are lots of tags that are supported by
WHHtmlCanvas view the class for a list of all methods that are available.

```
$html->anchor()->callback($this,"add")->with("++")
```

The above creates an anchor tag (a href) that performs the action $this->add()
when clicked. The callback method is something you will use a lot and it comes
in the form of

```
method callback(object,method,[array of optional arguments])
```

With input callbacks the first argument is always what the user inputs into the
input box and the optional arguments follow it.

You will notice that the add method simply adds one to the counter instance
variable. The value of all instance variables in your object is maintained by
Phaux. Go ahead and change add() to add 2 to $this->counter .

```
	public function add(){
		$this->counter += 2;
	}
```

Now click the "++" link. Notice that your number is incrimented by 2 this
time.

In a situation like this you may want add() and subtract() methods to be
called from buttons and not from a link. Phaux makes it easy to modify your
view without impacting the rest of your code.

Change renderContentOn() to look like the following.

```

	public function renderContentOn($html){
		return $html->form()->with(
			$html->div()->id("counter")->with(
				$html->headingLevel(1)->with(sprintf("%s",$this->counter)).
				$html->submitButton()->callback($this,"add")->value("++").
				$html->text(" ").
				$html->submitButton()->callback($this,"subtract")->value("--")
			)
		);
	
	}
```
Notice how we had to change very little to change the way WHCounter was
displayed. All form elements (buttons, inputs, textarea, select, etc) must be
in a form. The first thing we did was incapsulate the content in a form then
we changed the anchor method to submitButton() and the with method to value().

### Reduce, Reuse, Recycle: Component Interaction ###
At any time in a component call back you can call another component as a
dialog. A dialog component replaces the current component in the view and
assumes control of user input. Say for example you wanted to prevent your user
from going negative and instead display a message telling the user that
negatives are not allowed. Phaux ships with a simple component that can do
just that.

Change the subtract method of WHCounter to look like ...

```
	
	public function subtract(){
		if($this->counter == 0){
			$this->callDialog(Object::construct('WHInformDialog')->
								setMessage('Lets not get so negative'));
		}else{
			$this->counter--;
		}
	}

```

Now when you try to click "--" when the counter is at 0 Phaux displays "Lets
not get so negative". Clicking Okay will return control back to the WHCounter
component. A component returns control to the parent component by calling
$this->answer(). Some times it is useful to return a value from a dialog. We
can do that by calling answer() with an argument and setting the on answer
callback for that dialog.

Say instead of not allowing negatives we wanted to double check with the user
if negatives were ok to use. Phaux includs a dialog (WHYesNoDialog) that
displays a message, asks the user for a Yes or No answer, and answers with
TRUE or FALSE.

Change the subtract method to look like so ...

```
	
	public function subtract(){
		if($this->counter == 0){
			$this->callDialog(Object::construct('WHYesNoDialog')->
								setMessage('Are you sure you want to go negative?')->
								onAnswerCallback($this,'doSubtract'));
		}else{
			$this->doSubtract(TRUE);
		}
	}

```

And add the method doSubtract() ..

```
	public function doSubtract($aBool = TRUE){
		if($aBool){
			$this->counter--;
		}
	}

```

What happens when a user clicks Yes? How about No?

### Embedding Components ###
Embedding components is almost as easy as calling them as dialogs. Access
http://localhost/Phaux/HtmlRoot/phaux.php/multi. You should see 4 counters
with a border drawn around each. Take a look at the code for the
WHMultiCounter class. It's in the same directory as the WHCounter class.

WHMultiCounter defines 4 methods. The construct() method creates the
instances of the dialogs you want to embed. The don't have to be instantiated
in the constructor but they must be before they are drawn. The children()
method returns an array of the instances of all the dialogs you want to embed.
You must implement children() and it must return an array of instances of the
components you want to imbed not doing so will cause undefined behavior.

There are 2 other methods. The style() method can return a string of CSS that
you want to be used for this component. There are a few ways to include CSS
but the style() method is useful for development. Finaly renderContentOn()
tells Phaux how to draw the component.

```
public function renderContentOn($html){
	return $html->content(
		$html->div()->class("counter")->with(
			$html->render($this->counter1)
		).
		$html->div()->class("counter")->with(
			$html->render($this->counter2)
		).
		$html->div()->class("counter")->with(
			$html->render($this->counter3)
		).
		$html->div()->class("counter")->with(
			$html->render($this->counter4)
		).
		$html->div()->clear());

}

```

Tells Phaux to draw 4 div's with the instances of the counters embedded in
them. Notice how you don't call renderContentOn() of the embedded components
directly but instead call render() passing it the instance of the component
you want displayed. Calling renderContentOn directly will cause unpredictable
behavior.

### My Own Application ###

Each application has it's own simple config file. If you look in the
Configuration directory you will see a list of ini files. The name you give to
the file corresponds to the application name as it appears in the URL. All ini
files inherit there values from base.ini. Open up base.ini in your favorite
text editor.

The config file is divided up into sections general, server, includes, styles,
and scripts. You can add your own sections and values but creating custom
configurations is beyond the scope of this document. The general section
described some basic things about your application. The main\_class value is
the subclass of WHComponent that you want displayed to the user when first
come to you application.

The includes section list any files that you application needs in order to
work. The styles and scripts sections do the same thing for styles and scripts
that includes does for PHP files. The key name (for example Render) must be
unique.

Please, If you have any questions send an email to the Phaux-dev group,
http://groups.google.com/group/phaux-dev.








