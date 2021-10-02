<?php

session_start();
require_once 'utils/helpers.php';
session_destroy();
header('location: login.php');