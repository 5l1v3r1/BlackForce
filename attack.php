#!/usr/bin/php
<?php
echo "\e[1;31m
-----------------------------------------------------
 ____  _            _    ______
|  _ \| |          | |  |  ____|
| |_) | | __ _  ___| | _| |__ ___  _ __ ___ ___
|  _ <| |/ _` |/ __| |/ /  __/ _ \| '__/ __/ _ \
| |_) | | (_| | (__|   <| | | (_) | | | (_|  __/
|____/|_|\__,_|\___|_|\_\_|  \___/|_|  \___\___|
                                                v1.0
-----------------------------------------------------\e[0m
";

$striped = null;
if (isset($argv[1]) && in_array($argv[1], ["-ver", "--about"])) {

    about();

} elseif (in_array("--hash", $argv) || in_array("-hf", $argv) || in_array("--wordlist", $argv) || in_array("-w", $argv)) {
    $commands = ["--hash", "--wordlist", "-w", "-hf"];
    foreach ($argv as $value) {
        if (in_array($value, $commands)) {

        } else {

            $striped .= $value . "| BF |";
        }
    }

    $striped = rtrim($striped, "| BF |");

    $args = explode("| BF |", $striped);

    $lines = getLines($args[2]);
    $found = [];
    $i = 1;
    if ($file = fopen($args[1], "r")) {
        $text = fread($file, filesize($args[1]));
        $hashes = explode(PHP_EOL, $text);
        foreach ($hashes as $hash) {
            if (!is_null(trim($hash)) && !empty(trim($hash)) && trim($hash) !== "\n" && trim($hash) !== "\r") {
                echo "\n";
                echo "Attacking: " . $hash . "\n";
                echo "----------------------------" . "\n";
                if ($passwords = fopen($args[2], "r")) {
                    while (($password = fgets($passwords)) !== false) {
                        if (password_verify(trim($password), trim($hash))) {
                            echo "\e[1;32m";
                            echo "-------------------------------\n";
                            echo "Password Found: " . trim($password) . "\n";
                            echo "-------------------------------\n";
                            echo "\e[0m";
                            $found[] = [$hash => $password];
                            break;
                        } else {
                            if (in_array("--verbose", $argv) || in_array("-v", $argv)) {
                                echo "Check Password: " . trim($password) . "\n";
                            }

                            if ($i == $lines) {
                                echo "\e[1;31m";
                                echo "-------------------------------\n";
                                echo "Password not found ):\n";
                                echo "-------------------------------\n";
                                echo "\e[0m";
                                $i = 1;
                                break;
                            }
                        }
                        $i++;
                    }
                }
                fclose($passwords);
            }
        }
    }
    fclose($file);

    if (!empty($found)) {

        echo "Do you want to export result (Y/N): ";
        $option = readline("");
        if ($option == "Y" || $option == "y" || $option == "yes") {
            $filename = "result - " . date("Y-m-d h_m_s") . ".txt";
            $myfile = fopen($filename, "w+");
            fwrite($myfile, "[ BlackForce Result ]\n");
            fwrite($myfile, "------------------------\n");
            foreach ($found as $fph) {
                foreach ($fph as $hash => $password) {
                    fwrite($myfile, $hash . ":" . $password);
                }
            }
            fwrite($myfile, "------------------------\n");
            fclose($myfile);
            echo "Result Saved (: \n";
        } else {
            exit();
        }

    }
} else {
    help();
}

function getLines($file)
{
    $f = fopen($file, 'rb');
    $lines = 0;

    while (!feof($f)) {
        $lines += substr_count(fread($f, 8192), "\n");
    }

    fclose($f);

    return $lines;
}

function help()
{
    global $argv;
    echo "Usage:\n";
    echo basename($argv[0]) . " -hf <hashfile> -w <passwordlist>\n";
    echo "Example:\n";
    echo basename($argv[0]) . " -hf hash.txt -w passwords.txt\n";
    echo "-----------------------------------------------------\n";
}

function about()
{
    echo "name: BlackForce\n";
    echo "version 1.0\n";
    echo "developed by: Black.Hacker\n";
    echo "email: farisksa79@protonmail.com\n";
    echo "twitter: @BlackHacker_511\n";
}
?>
