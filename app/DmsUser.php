<?php

namespace App;

/**
 * Read/write access to user records owned by the DMS database.
 *
 * Keeping this separate from the authentication User model prevents
 * distributor relationships from accidentally querying admin_crms.users.
 */
class DmsUser extends User
{
    protected $connection = 'dms';
    protected $table = 'users';
}
