<?php

    include "utils/ipInRange.php";
    include "utils/core.php";

    $ipInRange = new ipInRange;
    $core = new core;

    // Database details
    $host = 'database_host';
    $db   = 'database';
    $user = 'database_user';
    $pass = 'database_pass';
    $charset = 'utf8';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);

    echo "Processing...<br>";

    echo "Policy: " . $_POST['Policy'] . "<br>";
    echo "Protocol: " . $_POST['InternetProtocol'] . "<br>";
    echo "Comment: " . $_POST['Comment'] . "<br>";
    echo "Action: " . $_POST['Action'] . "<br>";
    echo "Src Network: " . $_POST['SrcNetwork'] . "<br>";
    echo "Dst Network: " . $_POST['DstNetwork'] . "<br>";
    echo "Src Ports: " . $_POST['SrcPorts'] . "<br>";
    echo "Src Ports All: " . $_POST['SrcPortsAll'] . "<br>";
    echo "Dst Ports: " . $_POST['DstPorts'] . "<br>";
    echo "Dst Ports All: " . $_POST['DstPortsAll'] . "<br>";

    if($_POST['SrcPortsAll'] == "on") {
        $_POST['SrcPorts'] = "1-65535";
    }
    if($_POST['DstPortsAll'] == "on") {
        $_POST['DstPorts'] = "1-65535";
    }

    echo "Validation:<br>";

    $SrcPortsRange = $core->getValidPortRange($_POST["SrcPorts"]);
    $DstPortsRange = $core->getValidPortRange($_POST["DstPorts"]);
    if(!$core->isValidInt($_POST["Policy"])) {
        echo "Check your policy!";
    } elseif (!$core->isValidInt($_POST["SrcNetwork"])) {
        echo "Check your SrcNetwork!";
    } elseif (!$core->isValidInt($_POST["DstNetwork"])) {
        echo "Check your DstNetwork!";
    } elseif (!$core->isValidInt($_POST["InternetProtocol"])) {
        echo "Check your InternetProtocol!";
    } elseif (!$SrcPortsRange) {
        echo "Check your source port range!";
    } elseif (!$DstPortsRange) {
        echo "Check your destination port range!";
    } elseif (!($_POST["Action"] == 0 || $_POST["Action"] == 1)) {
        echo "Check your action!";
    } else {
        try {
            $pdo->beginTransaction();
            // Insert the source ports
            $stmt = $pdo->prepare("INSERT INTO Ports (Start,End) VALUES (:Start,:End)");
            $stmt->bindParam(':Start', $SrcPortsRange[0]);
            $stmt->bindParam(':End', $SrcPortsRange[1]);
            $stmt->execute();
            $SrcPortsID = $pdo->lastInsertId();
            // Insert the destination ports
            $stmt = $pdo->prepare("INSERT INTO Ports (Start,End) VALUES (:Start,:End)");
            $stmt->bindParam(':Start', $DstPortsRange[0]);
            $stmt->bindParam(':End', $DstPortsRange[1]);
            $stmt->execute();
            $DstPortsID = $pdo->lastInsertId();
            // Insert the policy information with relations with the source and destination network information
            $stmt = $pdo->prepare("INSERT INTO PolicyEntries (Comment,SrcNetwork,DstNetwork,InternetProtocol,SrcPorts,DstPorts,Action) VALUES (:Comment,:SrcNetworkID,:DstNetworkID,:InternetProtocol,:SrcPorts,:DstPorts,:Action)");
            $stmt->bindParam(':Comment', $_POST["Comment"]);
            $stmt->bindParam(':SrcNetworkID', $_POST['SrcNetwork'], PDO::PARAM_INT);
            $stmt->bindParam(':DstNetworkID', $_POST['DstNetwork'], PDO::PARAM_INT);
            $stmt->bindParam(':InternetProtocol', $_POST["InternetProtocol"], PDO::PARAM_INT);
            $stmt->bindParam(':SrcPorts', $SrcPortsID, PDO::PARAM_INT);
            $stmt->bindParam(':DstPorts', $DstPortsID, PDO::PARAM_INT);
            $stmt->bindParam(':Action', $_POST["Action"], PDO::PARAM_INT);
            $stmt->execute();
            $PolicyEntryID = $pdo->lastInsertId();
            // Insert link between the specified policy and new policy entry
            $stmt = $pdo->prepare("INSERT INTO `Policies-PolicyEntries` (PolicyEntryID,PolicyID) VALUES (:PolicyEntryID,:PolicyID)");
            $stmt->bindParam(':PolicyEntryID', $PolicyEntryID);
            $stmt->bindParam(':PolicyID', $_POST["Policy"]);
            $stmt->execute();
            // Commit
            $pdo->commit();
        } catch (Exception $e) {
            // Something went wrong with the try block, rollback.
            $pdo->rollback();
            echo "The information failed to be inserted!  Here is the PDO exception:<br>";
            echo $e->getMessage();
        }
    }
?>