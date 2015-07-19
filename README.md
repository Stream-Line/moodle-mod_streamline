# Streamline Setup
This is the main repo for our activity module.

Firstly, fork this repo into your own account. Then to install it on 
your moodle server, navigate to moodle/mod/ and run:
* git clone https://github.com/username/moodle-mod_streamline.git streamline <br>
Where username is your git username.
* Note the module has been setup for the 'streamline' name and will not work if changed.

Finally, make a new branch for your development:
* git checkout -b branchName <br>
Where branchName is whatever you want it to be. Try to be specific, don't
just put test1 or something.

Once your module is in a stable, working condition, submit a pull request.
Once someone else has reviewed your module, it will be pulled into the main
repo. Only once you have completed your module, should you attempt to merge 
your branch with the master. Once successful, you should go through the same
pull request process.

=====================================================================
# Original Setup Readme
The following steps should get you up and running with
this module template code.

* DO NOT PANIC!

* Unzip the archive and read this file

* Rename the newmodule/ folder to the name of your module (eg "widget").
  The module folder MUST be lower case and can't contain underscores. You should check the CVS contrib
  area at http://cvs.moodle.org/contrib/plugins/mod/ to make sure that
  your name is not already used by an other module. Registering the plugin
  name @ http://moodle.org/plugins will secure it for you.

* Edit all the files in this directory and its subdirectories and change
  all the instances of the string "newmodule" to your module name
  (eg "widget"). If you are using Linux, you can use the following command
  $ find . -type f -exec sed -i 's/newmodule/widget/g' {} \;

  On a mac, use:
  $ find . -type f -exec sed -i '' 's/newmodule/widget/g' {} \;

* Rename the file lang/en/newmodule.php to lang/en/widget.php
  where "widget" is the name of your module

* Rename all files in backup/moodle2/ folder by replacing "newmodule" with
  the name of your module

  On Linux you can perform this and previous steps by calling:
  $ find . -depth -name '*newmodule*' -execdir bash -c 'mv -i "$1" "${1//newmodule/widget}"' bash {} \;

* Place the widget folder into the /mod folder of the moodle
  directory.

* Go to Settings > Site Administration > Development > XMLDB editor
  and modify the module's tables.
  Make sure, that the web server has write-access to the db/ folder.
  You need at least one table, even if your module doesn't use it.

* Modify version.php and set the initial version of you module.

* Visit Settings > Site Administration > Notifications, you should find
  the module's tables successfully created

* Go to Site Administration > Plugins > Activity modules > Manage activities
  and you should find that this new module has been added to the list of
  installed modules.

* You may now proceed to run your own code in an attempt to develop
  your module. You will probably want to modify mod_form.php and view.php
  as a first step. Check db/access.php to add capabilities.

We encourage you to share your code and experience - visit http://moodle.org

Good luck!
