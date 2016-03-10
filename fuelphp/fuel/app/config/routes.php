<?php
return array(
	'_root_'  => 'practice1/index',  // The default route
	'_404_'   => 'practice1/404',    // The main 404 route

	'hello(/:name)?' => array('practice1/hello', 'name' => 'hello'),
);
