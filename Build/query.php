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

echo '<h1>Create Policy:</h1>';

?>
<form action="query.php" method="POST">
<label for="Name">Policy Name</label>
<input type="text" name="Name" placeholder="Policy Name">
<input type="submit" value="Create Policy">
</form>
<?

if(!empty($_POST["Name"])) {
    $stmt = $pdo->prepare("INSERT INTO Policies (Name) VALUES (:Name)");
    $stmt->bindParam(':Name', $_POST["Name"]);
    $stmt->execute();
}

echo '<hr>';

echo '<h1>Existing Policies:</h1>';
echo '<form action="query.php" method="POST">';
echo '<table border="1">';
echo '<tr>';
echo '<th>Select</th>';
echo '<th>ID</th>';
echo '<th>Name</th>';
echo '</tr>';
$stmt = $pdo->query('SELECT ID,Name FROM Policies');
while ($row = $stmt->fetch())
{
    echo '<tr>';
    echo '<td><input type="checkbox" name="checkbox[]" value="' . $row['ID'] . '">';
    echo '<td>' . $row['ID'] . "</td>";
    echo '<td>' . $row['Name'] . "</td>";
    echo '</tr>';
}
echo '</table>';
echo '<input type="submit" value="Delete Selected Policy(s)"/>';
echo '</form>';

if(is_array($_POST['checkbox'])){
    $stmt = $pdo->prepare("DELETE FROM Policies WHERE ID = ?");
    $i = 0;
    foreach($_POST["checkbox"] as $ID) {
        if(isset($ID)) {
            $stmt->execute([$ID]);
            $i++;
        }
    }
    echo "Deleted " . $i . " rows";
}
    

echo '<hr>';



?>
<h1>Delete Individual Policy:</h1>
<form action="query.php" method="POST">
<label for="ID">ID</label>
<input type="text" name="ID" placeholder="ID">
<input type="submit" value="Delete Individual Policy">
</form>
<?

if(!empty($_POST["ID"])) {
$stmt = $pdo->prepare("DELETE FROM Policies WHERE ID = :ID");
$stmt->execute([$_POST["ID"]]);
$deleted = $stmt->rowCount();
echo "Deleted " . $deleted . " rows";
}

echo '<hr>';

?>

<h1>Is IP in Subnet? (IPv4)</h1>

<p>Network can be specified as:</p>
<ul>
    <li>Wildcard format: 1.2.3.*</li>
    <li>CIDR format: 1.2.3/24  OR  1.2.3.4/255.255.255.0</li>
    <li>Start-End IP format: 1.2.3.0-1.2.3.255</li>
</ul>

<form action="query.php" method="POST">
    <label for="IP">IP Address</label>
    <input type="text" name="IP" placeholder="IP Address">
    <label for="Network">Network</label>
    <input type="text" name="Network" placeholder="Network">
    <input type="submit" value="Calculate">
</form>

<?

if(!empty($_POST["IP"]) && !empty($_POST["Network"])) {
    if($ipInRange->ipv4_in_range($_POST["IP"], $_POST["Network"])) {
        echo "True";
    } else {
        echo "False";
    }
}

echo '<hr>';




?>
<h1>Create Policy Entry:</h1>
<form action="query.php" method="POST">
<label for="Policy">Policy</label>
<select name="Policy">
<?
$stmt = $pdo->query('SELECT ID,Name FROM Policies');
while ($row = $stmt->fetch())
{
    echo '<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
}
?>    
</select><br>
<label for="Comment">Comment</label>
<input type="text" name="Comment" placeholder="Comment"><br>
<label for="SrcNetwork">SrcNetwork</label>
<input type="text" name="SrcNetwork" placeholder="SrcNetwork"><br>
<label for="SrcCIDRMask">SrcCIDRMask</label>
<input type="text" name="SrcCIDRMask" placeholder="SrcCIDRMask"><br>
<label for="DstNetwork">DstNetwork</label>
<input type="text" name="DstNetwork" placeholder="DstNetwork"><br>
<label for="DstCIDRMask">DstCIDRMask</label>
<input type="text" name="DstCIDRMask" placeholder="DstCIDRMask"><br>
<label for="InternetProtocol">InternetProtocol</label>
<select name="InternetProtocol">
<?
$stmt = $pdo->query('SELECT ID,Name FROM InternetProtocols');
while ($row = $stmt->fetch())
{
    echo '<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
}
?>   
</select>
<br>
<label for="SrcPorts">SrcPorts</label>
<input type="text" name="SrcPorts" placeholder="SrcPorts e.g. 445 or 1701-1702"><br>
<label for="DstPorts">DstPorts</label>
<input type="text" name="DstPorts" placeholder="DstPorts e.g. 445 or 1701-1702"><br>
<label for="Action">Action</label>
<select name="Action">
    <option value="0">Deny</option>
    <option value="1">Permit</option>
</select><br>
<input type="submit" value="Create Policy Entry">
</form>

<?

if(!empty($_POST["Policy"]) && !empty($_POST["Comment"])) {
    $SrcPortsRange = $core->getValidPortRange($_POST["SrcPorts"]);
    $DstPortsRange = $core->getValidPortRange($_POST["DstPorts"]);
    if(!$core->isValidInt($_POST["Policy"])) {
        echo "Check your policy!";
    } elseif (!$core->isValidIP($_POST["SrcNetwork"])) {
        echo "Check your SrcNetwork!";
    } elseif (!$core->isValidIP($_POST["DstNetwork"])) {
        echo "Check your DstNetwork!";
    } elseif (!$core->isValidCIDR($_POST["SrcCIDRMask"])) {
        echo "Check your SrcCIDRMask!";
    } elseif (!$core->isValidCIDR($_POST["DstCIDRMask"])) {
        echo "Check your DstCIDRMask!";
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
            // Insert the source network information
            $stmt = $pdo->prepare("INSERT INTO NetworksEndpoints (Network,CIDRMask) VALUES (INET6_ATON(:SrcNetwork),:SrcCIDRMask)");
            $stmt->bindParam(':SrcNetwork', $_POST["SrcNetwork"]);
            $stmt->bindParam(':SrcCIDRMask', $_POST["SrcCIDRMask"]);
            $stmt->execute();
            $SrcNetworkID = $pdo->lastInsertId();
            // Insert the destination network information
            $stmt = $pdo->prepare("INSERT INTO NetworksEndpoints (Network,CIDRMask) VALUES (INET6_ATON(:DstNetwork),:DstCIDRMask)");
            $stmt->bindParam(':DstNetwork', $_POST["DstNetwork"]);
            $stmt->bindParam(':DstCIDRMask', $_POST["DstCIDRMask"]);
            $stmt->execute();
            $DstNetworkID = $pdo->lastInsertId();
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
            $stmt->bindParam(':SrcNetworkID', $SrcNetworkID, PDO::PARAM_INT);
            $stmt->bindParam(':DstNetworkID', $DstNetworkID, PDO::PARAM_INT);
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
}

/*if(!empty($_POST["Policy"]) && !empty($_POST["Comment"])) {
    $stmt = $pdo->prepare("INSERT INTO PolicyEntries (Comment) VALUES (:Comment)");
    $stmt->bindParam(':Comment', $_POST["Comment"]);
    $stmt->execute();
    $lastInsertID = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO `Policies-PolicyEntries` (PolicyEntryID,PolicyID) VALUES (:PolicyEntryID,:PolicyID)");
    $stmt->bindParam(':PolicyEntryID', $lastInsertID);
    $stmt->bindParam(':PolicyID', $_POST["Policy"]);
    $stmt->execute();
}*/

?>
<hr>
<h1>Get Policy Entries for Policy:</h1>
<form action="query.php" method="POST">
<label for="Policy">Policy</label>
<select name="ChosenPolicy">
<?
$stmt = $pdo->query('SELECT ID,Name FROM Policies');
while ($row = $stmt->fetch())
{
    echo '<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
}
?>    
</select><br>
<input type="submit" value="Get Policy Entries">
</form>
<?

if(!empty($_POST["ChosenPolicy"])) {
    ?>
    <table border="1">
    <tr>
    <th>Comment</th>
    <th>SrcNetwork</th>
    <th>SrcCIDR</th>
    <th>DstNetwork</th>
    <th>DstCIDR</th>
    <th>InternetProtocol</th>
    <th>SrcStartPort</th>
    <th>SrcEndPort</th>
    <th>DstStartPort</th>
    <th>DstEndPort</th>
    <th>Action</th>
    </tr>
    <?
    $stmt = $pdo->prepare('SELECT PolicyEntries.Comment,
                                INET6_NTOA(NetworksEndpoints.Network) SrcNetwork,
                                NetworksEndpoints.CIDRMask SrcCIDR,
                                INET6_NTOA(a.Network) DstNetwork,
                                e.CIDRMask DstCIDR,
                                InternetProtocols.Name,
                                Ports.Start SrcStartPort,
                                b.Start DstStartPort,
                                c.End SrcEndPort,
                                d.End DstEndPort,
                                PolicyEntries.Action
                            FROM `Policies-PolicyEntries`,InternetProtocols,PolicyEntries
                            JOIN NetworksEndpoints
                            ON PolicyEntries.SrcNetwork = NetworksEndpoints.ID
                            JOIN NetworksEndpoints a
                            ON PolicyEntries.DstNetwork = a.ID
                            JOIN NetworksEndpoints e
                            ON PolicyEntries.DstNetwork = e.ID
                            JOIN Ports
                            ON PolicyEntries.SrcPorts = Ports.ID
                            JOIN Ports b
                            ON PolicyEntries.DstPorts = b.ID
                            JOIN Ports c
                            ON PolicyEntries.SrcPorts = c.ID
                            JOIN Ports d
                            ON PolicyEntries.DstPorts = d.ID
                            WHERE `Policies-PolicyEntries`.PolicyID = :ChosenPolicy
                            AND `Policies-PolicyEntries`.PolicyEntryID = PolicyEntries.ID
                            AND PolicyEntries.InternetProtocol = InternetProtocols.ID');
    $stmt->bindParam(':ChosenPolicy', $_POST['ChosenPolicy']);
    $stmt->execute();
    while ($row = $stmt->fetch())
    {
        echo '<tr>';
        echo '<td>' . $row['Comment'] . "</td>";
        echo '<td>' . $row['SrcNetwork'] . "</td>";
        echo '<td>' . $row['SrcCIDR'] . "</td>";
        echo '<td>' . $row['DstNetwork'] . "</td>";
        echo '<td>' . $row['DstCIDR'] . "</td>";
        echo '<td>' . $row['Name'] . "</td>";
        echo '<td>' . $row['SrcStartPort'] . "</td>";
        echo '<td>' . $row['SrcEndPort'] . "</td>";
        echo '<td>' . $row['DstStartPort'] . "</td>";
        echo '<td>' . $row['DstEndPort'] . "</td>";
        echo '<td>' . $row['Action'] . "</td>";
        echo '</tr>';
    }
    echo '</table>';
}

?>
<hr>
<h1>Create Network or Endpoint:</h1>
<form action="query.php" method="POST">
<label for="Network">Network</label>
<input type="text" name="Network" placeholder="Network">
<label for="CIDRMask">CIDRMask</label>
<input type="text" name="CIDRMask" placeholder="CIDR Mask">
<input type="submit" value="Create Network or Endpoint">
</form>
<?

if(!empty($_POST["Network"])) {
    if(filter_var($_POST["Network"], FILTER_VALIDATE_IP)) {
        if(!($_POST["CIDRMask"] >= 1 && $_POST["CIDRMask"] <= 128)) {
            $_POST["CIDRMask"] = NULL;
            echo "Your CIDR mask is not valid and has been set to NULL!";
        }
        $stmt = $pdo->prepare("INSERT INTO NetworksEndpoints (Network,CIDRMask) VALUES (INET6_ATON(:Network),:CIDRMask)");
        $stmt->bindParam(':Network', $_POST["Network"]);
        $stmt->bindParam(':CIDRMask', $_POST["CIDRMask"]);
        if($stmt->execute()) {
            echo "Inserted " . $stmt->rowCount() . " rows";
        } else {
            echo "Failed to insert that information.";
        }
    } else {
        echo "That isn't a valid IPv4/6 address!";
    }
}
        

$stmt = $pdo->query('SELECT ID,INET6_NTOA(Network) AS Network,CIDRMask
                    FROM NetworksEndpoints
                    WHERE CIDRMask IS NULL OR CIDRMask = "32"');
?>        
<hr>
<h1>Endpoints:</h1>
<table border="1">
<tr>
<th>ID</th>
<th>Endpoint</th>
<th>Hostname</th>
</tr>
<?
while ($row = $stmt->fetch())
{
    echo '<tr>';
    echo '<td>' . $row['ID'] . "</td>";
    echo '<td>' . $row['Network'] . "</td>";
    echo '<td>' . gethostbyaddr($row['Network']) . "</td>";
    echo '</tr>';
}
echo '</table>';
    
    

$stmt = $pdo->query('SELECT ID,INET6_NTOA(Network) AS Network,CIDRMask
                    FROM NetworksEndpoints
                    WHERE CIDRMask IS NOT NULL AND NOT CIDRMask = "32"');
?>        
<hr>
<h1>Networks:</h1>
<table border="1">
<tr>
<th>ID</th>
<th>Network</th>
<th>CIDRMask</th>
<th>Subnet Mask</th>
</tr>
<?
while ($row = $stmt->fetch())
{
    echo '<tr>';
    echo '<td>' . $row['ID'] . "</td>";
    echo '<td>' . $row['Network'] . "</td>";
    echo '<td>' . $row['CIDRMask'] . "</td>";
    echo '<td>' . $core->convertCIDRToSubnetMask($row['CIDRMask']) . "</td>";
    echo '</tr>';
}
echo '</table>';
    
    
    
?>
<h1>Networks and Endpoints:</h1>

<select>
    
    <option value="" disabled selected>Choose a Network or Endpoint:</option>
    <optgroup label="Networks">
    <?php
        
        $stmt = $pdo->query('SELECT ID,INET6_NTOA(Network) AS Network,CIDRMask
                        FROM NetworksEndpoints
                        WHERE CIDRMask IS NOT NULL AND NOT CIDRMask = "32"');

        while ($row = $stmt->fetch())
        {
            echo '<option value="'.$row['ID'].'">'.$row['Network'].'/'.$row['CIDRMask'].'</option>';
        }

    ?>
    </optgroup>
    
    <optgroup label="Endpoints">
    <?php
        
        $stmt = $pdo->query('SELECT ID,INET6_NTOA(Network) AS Network,CIDRMask
                        FROM NetworksEndpoints
                        WHERE CIDRMask IS NULL OR CIDRMask = "32"');

        while ($row = $stmt->fetch())
        {
            echo '<option value="'.$row['ID'].'">'.$row['Network'].'</option>';
        }

    ?>
    </optgroup>

</select>
    

    
    
<h1>Evaluate Host to Host Comms</h1>

<form action="query.php" method="POST">
<label for="Policy">Policy</label>
<select name="ChosenPolicy">
<?
$stmt = $pdo->query('SELECT ID,Name FROM Policies');
while ($row = $stmt->fetch())
{
    echo '<option value="'.$row['ID'].'">'.$row['Name'].'</option>';
}
?>    
</select><br>
<label for="SrcHost">SrcHost</label>
<input type="text" name="SrcHost" placeholder="SrcHost"><br>
<label for="DstHost">DstHost</label>
<input type="text" name="DstHost" placeholder="DstHost"><br>
<input type="submit" value="Evaluate">
</form>
    
<?
if(!empty($_POST["SrcHost"]) && !empty($_POST["DstHost"])) {
    if (!$core->isValidIP($_POST["SrcHost"])) {
        echo "Check your SrcHost!";
    } elseif (!$core->isValidIP($_POST["DstHost"])) {
        echo "Check your DstHost!";
    } else {
        $stmt = $pdo->prepare('SELECT PolicyEntries.Comment,
                                    INET6_NTOA(NetworksEndpoints.Network) SrcNetwork,
                                    NetworksEndpoints.CIDRMask SrcCIDR,
                                    INET6_NTOA(a.Network) DstNetwork,
                                    e.CIDRMask DstCIDR,
                                    InternetProtocols.Name,
                                    Ports.Start SrcStartPort,
                                    b.Start DstStartPort,
                                    c.End SrcEndPort,
                                    d.End DstEndPort,
                                    PolicyEntries.Action
                                FROM `Policies-PolicyEntries`,InternetProtocols,PolicyEntries
                                JOIN NetworksEndpoints
                                ON PolicyEntries.SrcNetwork = NetworksEndpoints.ID
                                JOIN NetworksEndpoints a
                                ON PolicyEntries.DstNetwork = a.ID
                                JOIN NetworksEndpoints e
                                ON PolicyEntries.DstNetwork = e.ID
                                JOIN Ports
                                ON PolicyEntries.SrcPorts = Ports.ID
                                JOIN Ports b
                                ON PolicyEntries.DstPorts = b.ID
                                JOIN Ports c
                                ON PolicyEntries.SrcPorts = c.ID
                                JOIN Ports d
                                ON PolicyEntries.DstPorts = d.ID
                                WHERE `Policies-PolicyEntries`.PolicyID = :ChosenPolicy
                                AND `Policies-PolicyEntries`.PolicyEntryID = PolicyEntries.ID
                                AND PolicyEntries.InternetProtocol = InternetProtocols.ID');
        $stmt->bindParam(':ChosenPolicy', $_POST['ChosenPolicy']);
        $stmt->execute();
        ?>
        <table border="1">
        <tr>
        <th>Comment</th>
        <th>SrcNetwork</th>
        <th>SrcCIDR</th>
        <th>DstNetwork</th>
        <th>DstCIDR</th>
        <th>InternetProtocol</th>
        <th>SrcStartPort</th>
        <th>SrcEndPort</th>
        <th>DstStartPort</th>
        <th>DstEndPort</th>
        <th>Action</th>
        </tr>
        <?
        while ($row = $stmt->fetch())
        {
            if($_POST["SrcHost"] == $row['SrcNetwork'] || $ipInRange->ipv4_in_range($_POST["SrcHost"], $row['SrcNetwork']."/".$row['SrcCIDR'])) {
                if($_POST["DstHost"] == $row['DstNetwork'] || $ipInRange->ipv4_in_range($_POST["DstHost"], $row['DstNetwork']."/".$row['DstCIDR'])) {
                    echo '<tr>';
                    echo '<td>' . $row['Comment'] . "</td>";
                    echo '<td>' . $row['SrcNetwork'] . "</td>";
                    echo '<td>' . $row['SrcCIDR'] . "</td>";
                    echo '<td>' . $row['DstNetwork'] . "</td>";
                    echo '<td>' . $row['DstCIDR'] . "</td>";
                    echo '<td>' . $row['Name'] . "</td>";
                    echo '<td>' . $row['SrcStartPort'] . "</td>";
                    echo '<td>' . $row['SrcEndPort'] . "</td>";
                    echo '<td>' . $row['DstStartPort'] . "</td>";
                    echo '<td>' . $row['DstEndPort'] . "</td>";
                    echo '<td>' . $row['Action'] . "</td>";
                    echo '</tr>';
                }
            }
        }
        echo '</table>';
    }
}
?>
    
<?
        
?>