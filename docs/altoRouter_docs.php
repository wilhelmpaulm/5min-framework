Altorouter

PHP5.3+ Routing Class. Supports REST, dynamic and reversed routing.

View the Project on GitHub
dannyvankooten/AltoRouter

Download ZIP  View on GitHub
Using AltoRouter
Mapping Routes

By now, you should have rewritten al requests to be handled by a single file in which you created an AltoRouter instance.

To map your routes, use the map() method. The following example maps all GET / requests.

$router->map( 'GET', '/', 'render_home', 'home' );
The map() method accepts the following parameters.

$method (string)
This is a pipe-delimited string of the accepted HTTP requests methods.

Example: GET|POST|PATCH|PUT|DELETE

$route (string)
This is the route pattern to match against. This can be a plain string, one of the predefined regex filters or a custom regex. Custom regexes must start with @. 

Examples:

Route	Example Match	Variables
/contact/	/contact/	
/users/[i:id]/	/users/12/	$id: 12
/[a:c]/[a:a]?/[i:id]?	/controller/action/21	$c: "controller", $a: "action", $id: 21
$target (mixed)
As AltoRouter leaves handling routes up to you, this can be anything.

Example using a function callback: 
function() { ... } 

Example using a controller#action string: 
UserController#showDetails

$name (string, optional)
If you want to use reversed routing, specify a name parameter so you can later generate URL's using this route.

Example:
user_details

Example Mapping

// map homepage using callable
$router->map( 'GET', '/', function() {
require __DIR__ . '/views/home.php';
});

// map users details page using controller#action string
$router->map( 'GET', '/users/[i:id]/', 'UserController#showDetails' );

// map contact form handler using function name string
$router->map( 'POST', '/contact/', 'handleContactForm' );
For quickly adding multiple routes, you can use the addRoutes method. This method accepts an array or any kind of traversable.

$router->addRoutes(array(
array('PATCH','/users/[i:id]', 'users#update', 'update_user'),
array('DELETE','/users/[i:id]', 'users#delete', 'delete_user')
));
Match Types

You can use the following limits on your named parameters. AltoRouter will create the correct regexes for you.

*                    // Match all request URIs
[i]                  // Match an integer
[i:id]               // Match an integer as 'id'
[a:action]           // Match alphanumeric characters as 'action'
[h:key]              // Match hexadecimal characters as 'key'
[:action]            // Match anything up to the next / or end of the URI as 'action'
[create|edit:action] // Match either 'create' or 'edit' as 'action'
[*]                  // Catch all (lazy, stops at the next trailing slash)
[*:trailing]         // Catch all as 'trailing' (lazy)
[**:trailing]        // Catch all (possessive - will match the rest of the URI)
.[:format]?          // Match an optional parameter 'format' - a / or . before the block is also optional
The character before the colon (the 'match type') is a shortcut for one of the following regular expressions

'i'  => '[0-9]++'
'a'  => '[0-9A-Za-z]++'
'h'  => '[0-9A-Fa-f]++'
'*'  => '.+?'
'**' => '.++'
''   => '[^/\.]++'
You can register your own match types using the addMatchTypes() method.

$router->addMatchTypes(array('cId' => '[a-zA-Z]{2}[0-9](?:_[0-9]++)?'));
Once your routes are all mapped you can start matching requests and continue processing the request.




Processing Requests

AltoRouter does not process requests for you so you are free to use the method you prefer. To help you get started, here's a simplified example using closures.

$router = new AltoRouter();

// map homepage
$router->map( 'GET', '/', function() {
	require __DIR__ . '/views/home.php';
});

// map user details page
$router->map( 'GET', '/user/[i:id]/', function( $id ) {
	require __DIR__ . '/views/user-details.php';
});

// match current request url
$match = $router->match();

// call closure or throw 404 status
if( $match && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}