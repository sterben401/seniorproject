<?php
session_start();
require_once 'config/db.php';

$groups = getGroups($conn);

foreach ($groups as $group) {
    echo '<div class="group-item">
            <span>' . htmlspecialchars($group['name']) . '</span>
            <a href="group.php?id=' . htmlspecialchars($group['id']) . '" class="btn btn-secondary">View Patterns</a>
          </div>';
}

