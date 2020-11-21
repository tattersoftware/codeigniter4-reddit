<?php

return [
	'failedResponse'   => 'API failed to respond for the following reason: {0}',
	'errorResponse'    => 'API responded with an error: {0}',
	'invalidSubreddit' => '{0} is not a valid subreddit',
	'missingSubreddit' => 'A valid subreddit is required.',
	'unverifiedPath'   => 'Unable to verify {0} in API response path "{1}".',
	'missingThingKey'  => 'The "{0}" key does not exist in this {1}.',
	'invalidThingName' => '"{0}" is not a valid identifier.',

	// Thing validation
	'thingMissingKind'       => 'API input has no kind.',
	'thingMissingData'       => 'API input has no data.',
	'thingInvalidData'       => 'API input data must be an object',
	'kindUnknownPrefix'      => '{0} is not a known Kind.',
	'kindMismatchedPrefix'   => 'Kind {0} does not match expected {1}',
	'kindMissingName'        => 'API input data has no name',
	'kindInvalidName'        => 'API input data has invalid name "{0}"',
	'listingMissingChildren' => 'API input data has no children',
	'listingInvalidChildren' => 'API input data "children" must be an array',
];
