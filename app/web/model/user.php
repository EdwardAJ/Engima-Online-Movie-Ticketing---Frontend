<?php
require_once('model_base.php');
class User extends Model
{
    public $id;
    public $username;
    public $email;
    public $pass;
    public $phone_number;
    public $profile_picture_url;
    public $login_hash;
}
