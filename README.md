# Lando + Drupal Contributions

This repo is intended to make it easy to contribute to the [Drupal core](https://drupal.org/project/drupal) and contrib projects.

- [Why](#why)
- [How](#how)
- [Testing Drupal Patches](#testing-drupal-patches)
- [Creating a Patch](#creating-a-patch)
- [Running Tests](#running-tests)
- [La Fin](#la-fin)

## Why?

Setting up, testing, and writing Drupal patches can be a confusing gauntlet to the uninitiated, [thinktandem/drupal-contributions](https://github.com/thinktandem/drupal-contributions) this project automates as much of the process as possible.

The spin ups should be considered completely ephemeral as on every `lando rebuild` events will be fired to tear down the current code base and rewrite the database with a fresh install.

Using this repo gives you a `.lando.yml` file configured for Drupal contributions:

- Automatically grabs the Drupal source code and runs `composer install` on `lando rebuild -y`
- Automatically kills the source code and database on `lando rebuild -y` so you can start fresh with each patch
- Automatically enables `simpletest`
- Adds a `lando test` command to invoke Drupal simpletests
- Adds a `lando si` command to reinstall the site with fresh DB if you need one (without rebuilding)
- Adds a `lando patch URL` command to pull down and apply a patch from drupal.org
- Adds a `lando revert PATCH_NAME` command should you need/want to revert a patch
- Adds a `lando create-patch` coommand to create a patch from the current branch

## How?

Video presentation: [SFDUG - June 25 - Lando for Contrib / LLC, Corporation or Sole Prop/Partnership](https://www.youtube.com/watch?v=vVpKCQZKNtM)

Let's step through how to spin up your contribution workflow. First clone down this repo:

```
git clone git@github.com:thinktandem/drupal-contributions.git
cd drupal-contributions
```

This gets us the `.lando.yml` config and scripts to glue all the processes together.

Next `rebuild` the `drupal-contributions` app:

> **_NOTE:_** Please note that we are using `rebuild` and not the `start` command. Rebuild has the events to trigger getting the Drupal source code and installation.

```
lando rebuild -y
```

This will pull in the drupal source code from the `9.2.x-dev` branch, run `composer install` to get dependencies, install Drupal, enable `simpletest` module, and provide us with a one time login link (`uli`).

After `rebuild` completes you should see something similar to this:

```
       ___                      __        __        __     __        ______
      / _ )___  ___  __ _  ___ / /  ___ _/ /_____ _/ /__ _/ /_____ _/ / / /
     / _  / _ \/ _ \/  ' \(_-</ _ \/ _ `/  '_/ _ `/ / _ `/  '_/ _ `/_/_/_/
    /____/\___/\___/_/_/_/___/_//_/\_,_/_/\_\\_,_/_/\_,_/_/\_\\_,_(_|_|_)

    Your app has started up correctly.
    Here are some vitals:

     NAME            drupal-contributions
     LOCATION        /home/gff/code/drupal-ops/drupal-contributions
     SERVICES        appserver, database
     APPSERVER URLS  https://localhost:33147
                     http://localhost:33148
                     http://drupal-contributions.lndo.site/
                     https://drupal-contributions.lndo.site/

```

and the `web` directory should be populated with the Drupal source code.

## Testing Drupal Patches

Now we are ready to find a Drupal issue. Search the issue queue for an `8.x` issue that you want to test. Grab the URL of the latest patch and apply it to our `drupal-contributions` environment.

For example if you choose this issue: https://www.drupal.org/project/drupal/issues/2962649, the latest corresponding patch (as of 25 June 2020) is https://www.drupal.org/files/issues/2019-09-12/2962649-10.patch. To apply this patch:

```
lando patch https://www.drupal.org/files/issues/2019-09-12/2962649-10.patch
```

To revert the patch:

```
lando revert 2962649-10.patch
```

This way we can `apply` and `revert` as many times as we want/need to during our testing.

To test this issue first add a file field to a content type say the page content type at `/admin/structure/types/manage/page/fields`. Now add a `page` node at `/admin/content/add/page` and add a text file to the file field you just made.

Visit the page in your web browser and click the file link. The file shows up in the current window. Now apply the patch with:

```
lando patch https://www.drupal.org/files/issues/2019-09-12/2962649-10.patch
```

and visit the `/admin/structure/types/manage/page/display` page and click through the gear to get to the settings of the `file_link` field we added to the `page` content type. You shoud now see a checkbox for `Open file in new tab (target=_blank)` which we can check and save.

Now visit the page or refresh the page in a web browser and click the file link. This time the file opens in a new tab!

The patch works!

We can now leave a comment on the issue saying that we tested the patch and it works as expected for us.

## Creating a Patch

If you are fixing a drupal.org issue, you should enter the `web` folder, checkout a branch using the prescribed naming conventions `ISSUE####-COMMENT#`. Write your code. Commit your code. Then you can utilize the `lando create-patch` to output the patch file based on your branch name.

```
lando create-patch
```

This will output a patch file to `/app/ISSUE####-COMMENT#.patch`, which you can upload to the drupal.org issue.

#### Core Patch Example
These are the steps required to create a patch. This example creates a branch, updates the `CHANGELOG.txt` core file, commits the update and creates the patch.

```
cd web
git checkout -b 987654-new-patch
echo "TEST" >> core/CHANGELOG.txt
git add core/CHANGELOG.txt
git commit -m "Updates CHANGELOG.txt"
lando create-patch
```

#### Contrib Module Example

To create a patch for a contrib module, for example [Admin Toolbar](https://www.drupal.org/project/admin_toolbar), download it to the modules folder, following the instructions under [Version control](https://www.drupal.org/project/admin_toolbar/git-instructions):

```
cd web/modules
git clone --branch 8.x-2.x https://git.drupalcode.org/project/admin_toolbar.git
```
Inside the contrib module folder, create a branch in the format `ISSUE####-COMMENT#`:

```
cd admin_toolbar
git checkout -b 1234567-admin_toolbar-improved-paths
```
When you are ready to create the patch, add any new files and updates to existing files, and create the patch:

```
git add -A
git diff 1234567-admin_toolbar-improved-paths > 1234567-admin_toolbar-improved-paths.patch
```

## Running Tests

When you create a patch you may have written tests for it that you want to run. At a minimum you'll want to run the tests for the module the patch is for to make sure your changes have not introduced regressions. To run the tests use the `lando test` command. To see what you can do use:

```
lando test --help
```

To run all the tests from the Database Logging module (`dblog`) for example use:

```
lando test --module dblog
```

To run a single test from the RDF module for example use:

```
lando test --file core/modules/rdf/tests/src/Functional/GetRdfNamespacesTest.php
```

#### PHPUnit
PHPUnit runs all the tests in Drupal 8 and above, to run tests with [PHPUnit](https://www.drupal.org/docs/automated-testing/phpunit-in-drupal/running-phpunit-tests):

List all tests groups:

```
lando phpunit --list-groups
```

Run one group of tests, for example BigPipe:

```
lando phpunit --group big_pipe
```

Run multiple groups of tests:

```
lando phpunit --group Group1,Group2
```

Exclude a group of tests:

```
lando phpunit --exclude-group Groupname
```

Run a single test from the BigPipe module:

```
lando phpunit web/core/modules/big_pipe/tests/src/Functional/BigPipeTest.php
```

## La Fin

Once you have the `9.2.x` you can keep it and sync it periodically and `lando start`'s will keep that around. If you want to totally start fresh:

```
# destroys drupal-contributions app and removes /web
lando destroy -y

# Spin up a fresh checkout of Drupal source installed and ready
# for dev, patching, and testing.
lando rebuild  -y
```

*Original text from [Lando + Drupal Contributions](https://blog.lando.dev/2020/06/30/lando-drupal-contributions/) by [Geoff St. Pierre](https://twitter.com/serundeputy).*
