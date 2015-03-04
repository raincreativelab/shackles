**Documentation work in progress**
---
# SHACKLES
## What is Shackles?
Shackle is a chainable, rule-based image processing library for PHP.
Simply put, it makes complex image processing damn simple and easy.

<a name="example"></a>
**A straight-forward example:** 

The following code will duplicate the original photo, crop at 400 x 300 pixel, 
then convert the whole thing into grayscale.

```php
// Create a new chain
$chain = new Chain();
$chain->setRules([
    new Duplicate(),
    new Crop([
        "width"  => 400,
        "height" => 300
    ]),
    new Grayscale()
]);

// Create a new runner
$runner = ChainBuilder::newRunner([
    "source" => "/path/to/my/image/directory",
    "format" => "gs-{NAME}-{###}-{W}x{H}"
]);

// Run the chain and apply to the image
$runner
    ->process("image.jpg", 100) // 100 is the JPG output quality
    ->run($chain);
    
// The following code will return the following image: 
// gs-image-15A-400x300.jpg
// The '15A' is a random string chain
```

## How Shackles work?
To better understand how Shackles do its thing, here are some basic terminologies
we will be using for clarity and consistency.

#### Rule 
is the basic unit of work. It contains how the image will be processed. 
There are 4 common rules:

  * [Resize](#resize)
  * [Duplicate](#duplicate)
  * [Crop](#crop)
  * [Convert Image to Grayscale](#grayscale)
  
The name of the rule clearly defines what it does and what you would expect for it
 to do. You can create define your own rules that you can attach to Shackle.
 
**You might want to know more about:**
 [Rules](#rule), [Creating your own Rule](#create-rule)
 
#### Chain 
are group of rules you chain to perform series of actions to your image.
The order of each rule depends on how it is plugged in to the chain.
 
**Learn how to:**
 [Create a Chain](#chain)
 
#### Runner 
runs the chain of rules you set. The runner accepts the image you want 
to process and use the chain of rules on how the target image will be processed.

**Learn how to:**
 [Run your Chain](#runner), [Chain Builder](#chain-builder), 
 [Storing your chain](#chain-serializer), 
 [Running Chain from String](#string-to-chain)
  
  
## Why use Shackles?
Shackles is just one of the components we use in our CMS. This component 
specifically handles one thing only - Image Processing. 

But because we want to share this to people that they might find it useful, 
we decided to make it a standalone component for people to integrate and use. 

#### Use case
There are scenarios that an image needs to be processed in a series of steps.
That steps can vary from resizing it, cropping it to produce thumbnails and 
sometimes, there is a need to produce multiple versions of your thumbnails in 
different aspect ratio. I know that there are other libraries out there to do just
 that or you might have something that you created your self. Well, 
 this kind of scenario is the thing Shackle is solving.
  
Shackle's decouple the processing and breaks them into a series of reusable, 
chainable rules that you can use, configure to solve different scenarios in image 
processing. Because of its simple design, and API, extending the component is 
simple and straight forward.

[You can always check the example above](#example)

<a name="rule"></a>
## Rule
Rule is the basic unit of work that defines how image will be processed or 
manipulated.

Any rule used by Shackle extends the ```shackles\Rule``` abstract class.
The abstract class requires the implementing class to implement the
 
```php
/**
 * This is a custom method that will be used
 * to process the image.
 *
 * @param Image $image Image to process
 *
 * @return void
 */
abstract protected function processImage(Image $image);
``` 

The following method signature should define how the image will be 
processed or manipulated.
