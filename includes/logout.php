<?php
session_start();
session_destroy();
header('Location: ../pages/login.php?success=You have been logged out.');
exit();