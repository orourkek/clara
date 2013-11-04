Clara\Database\Statement
========================

MySQL Statement classes, most notably used for building queries OOP style


NOTE
----

**This code is a work in progress, and should be used with caution. Please use GitLab to file bug reports, or talk to Kevin about it**


Query Building
--------------

The only query/statement type currently supported is `SELECT`. You can use the `\Clara\Database\Statement\Select` class to build MySQL SELECT queries programmatically. The end result will be an object that uses `__toString()` to return the compiled query when cast or treated as a string (you can also call `\Clara\Database\Statement\Select::compileStatement` directly).

All "non-getter" methods return back `$this` to allow for method chaining (see example below)


### Example Query converted to query builder

**BEFORE:**

    <?php
	$sql = "SELECT		`l`.`uid`					as `loginId`,
						`l`.`type`					as `type`,
						`s`.`uid`					as `siteId`,
						`s`.`name`					as `siteName`,
						`s`.`internationalname`		as `intlName`,
						`c`.`name`					as `country`

			FROM		`login`			as `l`,
						`site`			as `s`,
						`gcountry`		as `c`

			WHERE		`l`.`user_uid`		 = '".(int)$userId."'
			AND			`l`.`archive`		 = 0
			AND			`s`.`uid`			 = `l`.`site_uid`
			AND			`c`.`uid`			 = `s`.`gcid`

			ORDER BY	`l`.`type`,
						`c`.`name`";

**AFTER:**

    <?php
	$stmt = new Select();

	$stmt->columns(
		array('l.type', 'type'),
		array('s.uid', 'siteId'),
		array('s.name', 'siteName'),
		array('s.internationalname', 'intlName'),
		array('c.name', 'country')
	)

	$stmt->from('login', 'l')
		->from('site', 's')
		->from('country', 'c')

		->where('l.user_uid', '=', ':userUid')
		->andWhere('l.archive', '=', 0)
		->andWhere('s.uid', '=', 'l.site_uid')
		->andWhere('c.uid', '=', 's.gcid')

		->orderBy('l.type')
		->orderBy('c.name');