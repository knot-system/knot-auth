# Knot Auth

A small IndieAuth authorization & token endpoint, which is part of a larger system called [Knot System](https://github.com/knot-system). You can install it as a standalone service, or use the [Knot Installer](https://github.com/knot-system/knot-installer), which also installs other modules alongside it.

For now, this allows logging in via password. More methods will follow.

**This is an early beta version!** Some things may break, or change in the future!

## Requirements

A default webserver install / shared hosting service _should_ meet all requirements.

- PHP, at least version 8.0
- support for .htaccess files, with mod_rewrite
- write-access to the folder where this service is installed

## Initial Setup

Your server needs to run at least PHP 8.0 or later.

Copy all the files into a directory on your webserver, then open the url to this path in a webbrowser. Follow the installation instructions.

## Endpoints

The IndieAuth metadata endpoint is `/metadata`, so for example `https://www.example.com/metadata/`. This endpoint lists all other available endpoints. You should point the `indieauth-metadata` tag to this endpoint.

Other included endpoints are `/auth`, `/token` and `/revocation`.

There is also a URL to generate password hashes at `/generate-password`, or `https://www.example.com/generate-password/` (see "Additional Identities" below)

## Additional Identities

you can add additional Identities in the `config.php` file in the root folder. Use the `generate-password` endpoint (for example, `https://www.example.com/generate-password/` ) to generate a new password hash. Then add additional identities like this:

```php
<?php

return [
	'users' => [
		[
			'me' => 'https://www.example.com',
			'password' => '$2y$10$...'
		],
		[
			'me' => 'https://other-identity.example.com/',
			'password' => '$2y$10$...'
		]
	]
];

```

When you log in, the `me` URL is set automatically and you need to provide the corresponding password, to log in.

## Custom Theme

You can duplicate the `theme/default/` folder, rename it and update the theme name and author information in the `theme/{themename}/theme.php`.

You can define the theme your site uses in the `config.php` file like this:
```php
return [
	// site_title and other fields ...
	'theme' => '{themename}',
];
```

If the theme folder does not exist, the system will automatically use the *default* theme.

You can also create a `theme/{themename}/snippets/` folder and copy files from `system/site/snippets/` into this folder, to overwrite them on a per-theme basis. All the files in the `snippets/` folder have a version number at the start of the file, so you can see if they were updated since you last copied them. The auto-updater will also show you, which of the snippets in your custom theme are out of date and need updating.

The `theme/{themename}/functions.php` contains some functions that get called when the theme gets loaded.

The `theme/{themename}/config.php` can overwrite config options from `system/config.php` (but gets itself overwritten by the local `config.php` in the root directory), so the custom theme can for example set its own image sizes.

## Updating

**Important:** Before updating, backup your `config.php` (and your custom theme inside the `theme/` folder, if you have any). Better be safe than sorry.

You can use [Knot Control](https://github.com/knot-system/knot-control) to update this module automatically. Or you use the following instructions:

Create a new empty file called `update` (or `update.txt`) in the root folder of your installation. Then open the website, and append `?update` to the URL to trigger the update process. **Important:** if you don't finish the update, manually delete the `update` (or `update.txt`) file (if the update process finishes, this file gets deleted automatically).

Follow the steps of the updater. It will show you all of the new release notes that happened since your currently installed version - read them carefully, especially at the moment, because some things will change and may need manual intervention. After the update is complete, and if you have a custom theme installed, it will list all the files you manually need to update in your custom theme - you should do so, or you may miss out on new functionality, or the site may even break completely.

After updating, open your url in a webbrowser; this will trigger the setup process again, that creates some missing files. Then check if everything works as expected.

### manual update

If you want to perform a manual update, delete the `system/`, `theme/default/` and `cache/` folders, as well as the `index.php`, `.htaccess`, `README.md` and `changelog.txt` files from the root folder. Then download the latest (or an older) release from the releases page. Upload the `system/` and `theme/default/` folders and the `index.php`, `README.md` and `changelog.txt` file from the downloaded release zip-file into your web directory. Then open the url in a webbrowser; this will trigger the setup process again and create some missing files.

If you have a custom theme active, make sure all your snippets are up to date and at least the same version as the corresponding files inside `system/site/snippets/`.

### system reset

If you want to reset the whole system, delete the following files and folders and open the url in a webbrowser to re-trigger the setup process:
- `.htaccess`
- `config.php`
- the `cache/` folder
- maybe the custom theme folders in the `theme/` directory (leave the `theme/default/` directory there, though)

## Backup

You should routinely backup your content. To do so, copy these files & folders to a secure location:

- the `config.php`. This contains all your identities and passwords, which theme you use and other settings
- if you have a custom theme inside the `theme/` directory, make a backup of it as well. The `theme/default/` theme comes with the system, so no need to back it up

When you want to restore a backup, delete the current folders & files from your webserver, and upload your backup. You should also delete the `cache/` folder, so everything gets re-cached and is up to date. If you also want to update or reset your system, see the *Update* section above.
