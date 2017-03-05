#Laravel Relatables

This code snippet allows users to create new Eloquent objects with embedded One-to-Many and Polymorphic Many-to-Many
relationships. Although the snippet is intended to be used as sample code for Samuel Visscher, feel free to use it if
you may seem use for it.

##Installation

As this is a sample code snippet, I have not (yet) created an installation through packagist for it.

1. Copy the src folder to your project (e.g. at /lib/relatables).
2. Add this folder to your package.json file, typically you would have to update the autoload psr-4 to:

        "psr-4": {
            "App\\": "app/",
            "Visscher\\Relatables\\": "lib/relatables/src/"
        }
        
3. Add the ServiceProvider to the providers array in `config/app.php`
4. Execute an `composer dump-autoload`

##Usage

Once installed, using this package is as easy as using the `HasRelatables` trait and adding any of the model's relations
to the relatable property which it should accept (and be accepted as) embedded objects in their create and update 
methods. Note: the trait has to be present on both objects, and requires the relation fields to be fillable.

Imagine an Order object:

    class Order extends Model
    {
        public $fillable = ['name'];
    
        public function notes()
        {
            return $this->morphMany(Note::class, 'notable');
        }
    }

Which should accept an embedded Note object:

    class Note extends Model
        {
            public $fillable = ['content', 'notable_id', 'notable_type'];
        
            public function notable()
            {
                return $this->morphTo();
            }
    }

We want to be able to execute the following method:

    Order::create([
        'name': 'A new computer'
        'notes': [
            ['content => 'No delivery before 9 am please!']
        ]);
        
Using this package, we could allow this functionality by simply updating our two objects:

    class Order extends Model
    {
        use HasRelatables;
        
        protected $relatables = ['notes'];
    
        public $fillable = ['name'];
    
        public function notes()
        {
            return $this->morphMany(Note::class, 'notable');
        }
    }
    
    class Note extends Model
        {
            use HasRelatables;
        
            public $fillable = ['content', 'notable_id', 'notable_type'];
        
            public function notable()
            {
                return $this->morphTo();
            }
    }
    
The package supports various edge cases, where for example, you would want to update an existing object, to be connected
to a new Order. But that's outside the scope of this code snippet.