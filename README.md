# li3_mixpanel

Lithium library for sending statistical data to [Mixpanel](https://mixpanel.com).

## Installation

Add a submodule to your li3 libraries:

	git submodule add git@github.com:bruensicke/li3_mixpanel.git libraries/li3_mixpanel

and activate it in you app (config/bootstrap/libraries.php), of course:

	Libraries::add('li3_mixpanel');

## Usage

### Preparation

	Mixpanel::$token = 'de3b97e4e2807f5addb8c746c3a6f6e5';

### Sending data

	Mixpanel::track('api.requests', $params['request']->params);

## Credits

* [li3](http://www.lithify.me)
* [Mixpanel](https://mixpanel.com)


