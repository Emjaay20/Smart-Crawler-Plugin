<?php
// This file is generated. Do not modify it manually.
return array(
	'smart-content-fetcher' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'create-block/smart-content-fetcher',
		'version' => '0.1.0',
		'title' => 'Smart Content Fetcher',
		'category' => 'widgets',
		'icon' => 'smiley',
		'description' => 'Example block scaffolded with Create Block tool.',
		'attributes' => array(
			'url' => array(
				'type' => 'string'
			),
			'fetchedTitle' => array(
				'type' => 'string'
			),
			'fetchedSummary' => array(
				'type' => 'string'
			),
			'lastFetched' => array(
				'type' => 'number'
			)
		),
		'example' => array(
			
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'smart-content-fetcher',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js'
	)
);
