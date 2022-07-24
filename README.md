# Lando + Drupal Contributions

This repo is intended to make it easy to contribute to the [Drupal core](https://drupal.org/project/drupal) and contrib projects.

- [Lando + Drupal Contributions](#lando--drupal-contributions)
  - [Why?](#why)
  - [How?](#how)
  - [Testing Drupal Patches](#testing-drupal-patches)
      - [Test a Core Patch](#test-a-core-patch)
      - [Test a Contrib Module Patch](#test-a-contrib-module-patch)
  - [Creating a Patch](#creating-a-patch)
      - [Core Patch Example](#core-patch-example)
      - [Contrib Module Example](#contrib-module-example)
  - [Running Tests](#running-tests)
      - [PHPUnit](#phpunit)
      - [Nightwatch](#nightwatch)
  - [La Fin](#la-fin)

## Why?

Setting up, testing, and writing Drupal patches can be a confusing gauntlet to the uninitiated, [thinktandem/drupal-contributions](https://github.com/thinktandem/drupal-contributions) this project automates as much of the process as possible.

The spin ups should be considered completely ephemeral as on every `lando rebuild` events will be fired to tear down the current code base and rewrite the database with a fresh install.

Using this repo gives you a `.lando.yml` file configured for Drupal contributions:

- Automatically grabs the Drupal source code and runs `composer install` on `lando rebuild -y`
- Automatically kills the source code and database on `lando rebuild -y` so you can start fresh with each patch
- Adds a `lando phpunit` command to invoke PHPUnit tests
- Adds a `lando si` command to reinstall the site with fresh DB if you need one (without rebuilding)
- Adds a `lando patch URL` command to pull down and apply a patch from drupal.org
- Adds a `lando revert PATCH_NAME` command should you need/want to revert a patch
- Adds a `lando core-check` coommand to check code standards and spelling
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

This will pull in the drupal source code from the latest `9.x-dev` branch, run `composer install` to get dependencies, install Drupal, and provide us with a one time login link (`uli`). You can update the version in the `/config/drupal-branch.php` file.

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

#### Test a Core Patch

Now we are ready to find a Drupal core issue. Search the issue queue for a core `9.x` issue that you want to test. Grab the URL of the latest patch and apply it to our `drupal-contributions` environment.

For example if you choose this issue: https://www.drupal.org/project/drupal/issues/3186076, the latest corresponding patch (as of 20 January 2021) is https://git.drupalcode.org/project/drupal/-/merge_requests/161.diff ("plain diff" link). To apply this patch:

```
lando patch https://git.drupalcode.org/project/drupal/-/merge_requests/161.diff
```

Note: Both Gitlab-patches with `.diff` suffix as well as the old style `.patch` files will work.

To revert the patch:

```
lando revert 161.diff
```

This way we can `apply` and `revert` as many times as we want/need to during our testing.

To test this issue, apply the patch as outlined above, clear caches, and visit for example `/admin/structure/views/view/frontpage`, and see that the "Tour" link has been turned blue, and the text extended to "Take a tour of this page".

The patch works!

We can now leave a comment on the issue saying that we tested the patch and it works as expected for us

#### Test a Contrib Module Patch

At the moment, Lando + Drupal Contributions doesn't `automate` contrib module patch testing and creating. In the meantime, you can still use the `drupal-contributions` environment to test and create contrib module patches as you would otherwise. One simple method is to search the contrib module issue queue for an issue that you want to test. Grab the URL of the latest patch, and apply it the contrib module in our `drupal-contributions` environment.

For example, if you choose the geofield contrib module and this issue: https://www.drupal.org/project/geofield/issues/2770313, the latest corresponding patch (as of 2 June 2019) is https://www.drupal.org/files/issues/2019-06-02/geofield-add-GeometryType-storaging-2770313-26.patch. To apply this patch:

```
cd web/modules;
git clone https://git.drupalcode.org/project/geofield.git;
cd geofield;
wget https://www.drupal.org/files/issues/2019-06-02/geofield-add-GeometryType-storaging-2770313-26.patch;
git apply -v geofield-add-GeometryType-storaging-2770313-26.patch;
```

Note: Both Gitlab-patches with `.diff` suffix as well as the old style `.patch` files will work.

If the patch fails, try to figure out why. For example, run its phpunit tests:

```
lando phpunit --group geofield;
```

If you discover anything useful, leave a comment on the issue at drupal.org.

To revert the patch:

```
lando revert geofield-add-GeometryType-storaging-2770313-26.patch
```

This way we can `apply` and `revert` as many times as we want/need to during our testing.

## Creating a Patch

#### Core Patch Example

If you are fixing a drupal.org core issue, you should enter the `web` folder, checkout a branch using the prescribed naming conventions `ISSUE####-COMMENT#`. Write your code. Commit your code. Check your code using `lando core-check`. Then you can utilize the `lando create-patch` to output the patch file based on your branch name.

```
lando core-check
```

This will run the same tests that the testbot runs before running the actual
PHPUnit tests: spell check, CodeSniffer, etc. You can ignore reports that your
files have permissions 664 instead of 644.

```
lando create-patch
```

This will output a patch file to `/app/ISSUE####-COMMENT#.patch`, which you can upload to the drupal.org issue.

For example, these are the steps required to create a core patch. This example creates a branch, updates the `CHANGELOG.txt` core file, commits the update and creates the patch.

```
cd web
git checkout -b 987654-new-patch
echo "TEST" >> core/CHANGELOG.txt
git add core/CHANGELOG.txt
git commit -m "Updates CHANGELOG.txt"
lando core-check
lando create-patch
```

#### Contrib Module Example

To create a contrib module patch, for example [Admin Toolbar](https://www.drupal.org/project/admin_toolbar), download it to the modules folder, following the instructions under [Version control](https://www.drupal.org/project/admin_toolbar/git-instructions):

```
cd web/modules
git clone --branch 8.x-2.x https://git.drupalcode.org/project/admin_toolbar.git
```

Inside the contrib module folder, create a branch in the format `ISSUE####-COMMENT#`. For example:

```
cd admin_toolbar
git checkout -b 1234567-admin_toolbar-improved-paths
```

Make your changes.

```
...(code code code)...
```

Test your changes. For example:

```
cd ../drupal-contributions;
lando phpunit --group admin_toolbar;

// or run a single test

lando phpunit web/modules/admin_toolbar/tests/src/Functional/AdminToolbarAlterTest.php
```

When you are ready, add/commit the relevant changes and create the patch. For example:

```

git add this.php that.js
git commit -m "Your commit message"

// While still on your patch branch, git diff the module's dev branch and redirect its output to a file in the format ISSUE####-COMMENT#.patch. For example:

git diff 8.x-2.x > 1234567-admin_toolbar-improved-paths.patch
```

Verify your own contrib module patch against the module's dev branch. For example:

```
git checkout 8.x-2.x;
git apply -v 1234567-admin_toolbar-improved-paths.patch;

// Verify output of above command is:

Checking patch...
Applied patch...cleanly.
```

If the patch works, upload it to the drupal.org contrib module issue.

## Running Tests

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

Run a single test from the Password module:

```
lando phpunit web/core/tests/Drupal/Tests/Core/Password/PasswordHashingTest.php
```

#### Nightwatch

To run only core tests, run:

```
lando nightwatch --tag core
```

To skip running core tests, run:

```
lando nightwatch --skiptags core
```

To run a single test, run e.g:

```
lando nightwatch tests/Drupal/Nightwatch/Tests/exampleTest.js
```

## La Fin

Once you have the latest `9.x-dev` branch you can keep it and sync it periodically and `lando start`'s will keep that around. If you want to totally start fresh:

```
# destroys drupal-contributions app and removes /web
lando destroy -y

# Spin up a fresh checkout of Drupal source installed and ready
# for dev, patching, and testing.
lando rebuild  -y
```

*Original text from [Lando + Drupal Contributions](https://lando.dev/blog/2020/06/02/lando-drupal-contributions/) by [Geoff St. Pierre](https://twitter.com/serundeputy).*
