REQUIREMENTS
------------

- PHP >= 4
- CakePHP >= 1.2

NOTES
-----

Enumerable is a CakePHP plugin.

This is not trying to emulate ENUM column type.

A simple way to understand on what database model this behavior is built onto,
consider using User and Role.

Enumerable is attached to the Role model and you can access it directly through:
	/* in your User model */
	$roleId = $this->Role->enum('admin'); // name of your role
	$roleName = $this->Role->enum(1); // id of your role

By default, Enumerable use the displayField of your model. (this can be configured using option `fieldList`)

A few options exist and can be use like:
* `fieldList`
* `conditions`
* `cache`

More info in the PHP doc.

INSTALL
-------

For those who use submodules in their Git repository:
	/* Console */
	cd app/plugins/
	git clone <url_of_enumerable_behavior> enumerable
	git submodule add enumerable

Attach this behavior using:
	/* in any enumerable model */
	$actsAs = array('Enumerable.Enumerable');
