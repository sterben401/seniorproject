<?php
    require_once 'config/db.php';
  
    function readLogFile($filename) {
        $lines = file($filename);
        $log_data = array();
        $md5_hashes = array(); // Initialize an empty entry
        $current_entry = array(); // Initialize an empty entry for a log entry
    
        global $conn; // Assuming $conn is your database connection
    
        foreach ($lines as $line) {
            $md5_hash = md5($line);
    
            // Check if the current line contains "Date and Time:"
            if (strpos($line, "Date and Time:") !== false) {
                $current_entry['Date and Time'] = trim(str_replace("Date and Time:", "", $line));
            } elseif (strpos($line, "Login:") !== false) {
                $current_entry['Login'] = trim(str_replace("Login:", "", $line));
                $current_entry['Login'] = strip_tags($current_entry['Login']); // Strip the quotes from the login entry
            } elseif (strpos($line, "Password:") !== false) {
                $current_entry['Password'] = trim(str_replace("Password:", "", $line));
                $current_entry['Password'] = strip_tags($current_entry['Password']); // Strip the quotes from the password entry
            }
    
            // Check if the current entry has both 'Login' and 'Password' keys
            if (isset($current_entry['Login']) && isset($current_entry['Password'])) {
                $log_data[] = $current_entry; // Add the current log entry to $log_data
                $current_entry = array(); // Reset the current entry for the next log entry
            }
    
            // Check if the MD5 hash already exists in the "history" table
            $stmt_check = $conn->prepare("SELECT COUNT(*) FROM history WHERE md5 = :md5");
            $stmt_check->bindParam(':md5', $md5_hash);
            $stmt_check->execute();
            $count = $stmt_check->fetchColumn();
    
            if ($count == 0) {
                // Insert the MD5 hash into the "history" table
                $stmt_insert = $conn->prepare("INSERT INTO history (md5) VALUES (:md5)");
                $stmt_insert->bindParam(':md5', $md5_hash);
                $stmt_insert->execute();
            }
    
            // Check if the current entry has "Date and Time" data
            if (isset($current_entry['Date and Time'])) {
                $dateAndTime = $current_entry['Date and Time'];
    
                // Insert the "Date and Time" data into the "history" table under the "Data_his" column
                $stmt_update_date_time = $conn->prepare("UPDATE history SET Data_his = :dateAndTime WHERE md5 = :md5");
                $stmt_update_date_time->bindParam(':dateAndTime', $dateAndTime);
                $stmt_update_date_time->bindParam(':md5', $md5_hash);
                $stmt_update_date_time->execute();
    
                // Reset the current entry
                $current_entry = array();
            }
        }
    
        $_SESSION['md5_hashes'] = $md5_hashes;
        return $log_data;
    }
    
    
    
    function getPatterns() {
        global $conn;
        $sql = "SELECT namePattern, md5 FROM pattern";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function findMatchingPatterns($log_data, $patterns) {
    $matches = array();

    foreach ($log_data as $log_entry) {
        foreach ($patterns as $pattern) {
            if ($log_entry === $pattern['md5']) {
                $matches[] = array(
                    'Pattern' => $pattern['namePattern'],
                    'Count' => 1, // You may need to update this based on your requirements
                    'Status' => 'MATCH' // You may need to define status logic
                );
            }
        }
    }

    return $matches;
}
          function processLogData($log_data, &$history) {
            $patterns = getPatterns();
            $history = array();
            $md5Patterns = array();
        
            global $conn;
            $sql = "SELECT namePattern, md5 FROM pattern";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $md5Patterns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($log_data as $logEntry) {
                $login = $logEntry['Login'];
                $password = $logEntry['Password'];
                $combinedData = $login . ' ' . $password;
        
                $matched = false;
                foreach ($patterns as $pattern) {
                    if (stripos($login, $pattern['namePattern']) !== false || stripos($password, $pattern['namePattern']) !== false) {
                        $matched = true;
                        break;
                    }
                }
        
                if ($matched) {
                    $found = false;
                    foreach ($history as &$item) {
                        if ($item[0] == $login && $item[1] == $password) {
                            $item[2]++;
                            $found = true;
                            break;
                        }
                    }
        
                    if (!$found) {
                        $history[] = array($login, $password, 1);
                    }
                }
            }
        
            $threshold = 10;
            foreach ($history as &$item) {
                $count = $item[2];
                $status = 'GREEN'; // Default status
        
                if ($count >= 10) {
                    $status = 'RED';
                } elseif ($count >= 5) { // You can adjust this threshold as needed
                    $status = 'ORANGE';
                }
        
                $item[] = $status;
            }
        }
          
        $_SESSION['log_data'] = readLogFile("log0.txt");
        $history = array();
        $final_data = processLogData($_SESSION['log_data'], $history);
          //$history = 
    ?>

