Users
=====

The following use cases must be implemented for the users module:

Login user
----------
:heavy_check_mark: A user must be able to login.

Automatic reactivate
--------------------
:heavy_check_mark: A user gets an accesstoken, it must be possible to renew this using a refreshtoken.

Get current user
----------------
:heavy_check_mark: It must be possible to get the abilities of the logged in user

Logout
------
:heavy_check_mark: A user must be able to logout.

Change password
---------------
:x: A user must be able to change its password.

Change email
------------
:x: A user must be able to change its email address. The email address must be
checked.

Register user
-------------
:x: A visitor must be able to register for the website.

Invite user
-----------
:heavy_check_mark: An administrator of the site must be able to invite another user.

Confirm invitation
------------------
:heavy_check_mark: A new user must be able to confirm the invitation to create a new account.

Approve user
------------
:x: An administrator must be able to approve a registration of a user (when the
user wasn't invited by the administrator).

Revoke user
-----------
:x: An administrator must be able to revoke the access of a user.

Permissions
-----------
:heavy_check_mark: It must be possible to give a user permissions to perform selected actions
on the system.

Revoke permission
-----------------
:heavy_check_mark: An administrator of the website must be able to revoke a permission from a user.
