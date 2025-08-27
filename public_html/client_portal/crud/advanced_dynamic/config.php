<?php
// Database credentials
$db_host = 'localhost';
// Database name
$db_name = 'phpcrud';
// Database user
$db_user = 'root';
// Database password
$db_pass = '';
// Table
$table = 'contacts';
// Column list
$columns = [
	'id' => [
		'label' => '#',
		'sortable' => true,
		'type' => 'integer'
	],
	'first_name' => [
		'label' => 'First Name',
		'sortable' => true,
		'type' => 'string',
		'input' => [
			'placeholder' => 'John',
			'type' => 'text',
			'required' => true,
			'validate_msg' => 'First name must be between 1 and 50 characters!',
			'validate_regex' => '/^[a-zA-Z]{1,50}$/'
		]
	],
	'last_name' => [
		'label' => 'Last Name',
		'sortable' => true,
		'type' => 'string',
		'input' => [
			'placeholder' => 'Doe',
			'type' => 'text',
			'required' => true,
			'validate_msg' => 'Last name must be between 1 and 50 characters!',
			'validate_regex' => '/^[a-zA-Z]{1,50}$/',
		]
	],
	'email' => [
		'label' => 'Email',
		'sortable' => true,
		'type' => 'string',
		'input' => [
			'placeholder' => 'Email Address',
			'type' => 'email',
			'required' => true,
			'validate_msg' => 'Please enter a valid email address!',
			'validate_regex' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
		]
	],
	'phone' => [
		'label' => 'Phone',
		'sortable' => false,
		'type' => 'string',
		'input' => [
			'placeholder' => 'Phone Number',
			'type' => 'tel',
			'required' => true,
			'validate_msg' => 'Please enter a valid phone number!',
			'validate_regex' => '/^[0-9]{8,15}$/',
		]
	],
	'title' => [
		'label' => 'Title',
		'sortable' => true,
		'type' => 'string',
		'input' => [
			'placeholder' => 'Title',
			'type' => 'select',
			'options' => ['Employee', 'Manager', 'CEO'],
			'required' => true,
			'validate_msg' => 'Title must be between 1 and 50 characters long!',
			'validate_regex' => '/^[a-zA-Z]{1,50}$/',
		]
	],
	'created' => [
		'label' => 'Created',
		'sortable' => true,
		'type' => 'datetime',
		'input' => [
			'placeholder' => 'Created',
			'type' => 'datetime-local',
			'required' => true,
			'validate_msg' => 'Please enter a valid date!',
			'validate_regex' => '/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}$/',
		]
	]
];
// Default column
$default_column = 'id';
// Default records per page
$default_records_per_page = 5;
?>