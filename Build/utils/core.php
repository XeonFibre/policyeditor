<?php

    class core {
        
        // Creates a new policy
        function createPolicy($Name) {
            $stmt = $pdo->prepare("INSERT INTO Policy (Name) VALUES (:Name)");
            $stmt->bindParam(':Name', $_POST["Name"]);
            $stmt->execute();
        }
        
        // Returns all policies as an array
        function getPolicies() {
            return $this->pdo->query('SELECT ID,Name FROM Policy');
        }
        
        // Converts CIDR notation to subnet mask
        // From: https://gist.github.com/linickx/1309388#file-cidr2mask-php
        function convertCIDRToSubnetMask($CIDR) {
            if(($CIDR >= 1 && $CIDR <= 32)) {
                $netmask_result="";
                for($i=1; $i <= $CIDR; $i++) {
                  $netmask_result .= "1";
                }
                for($i=$CIDR+1; $i <= 32; $i++) {
                    $netmask_result .= "0";
                }
                $netmask_ip_binary_array = str_split( $netmask_result, 8 );
                $netmask_ip_decimal_array = array();
                foreach( $netmask_ip_binary_array as $k => $v ){
                    $netmask_ip_decimal_array[$k] = bindec( $v ); // "100" => 4
                }
                $subnet = join( ".", $netmask_ip_decimal_array );
                return $subnet;
            } else {
                return false;
            }
        }
        
        // Validate an IP address
        function isValidIP($IP) {
            if(filter_var($IP, FILTER_VALIDATE_IP)) {
                return true;
            }
        }
        
        // Validate an IPv4 and IPv6 CIDR prefix
        function isValidCIDR($CIDR) {
            if($CIDR >= 0 && $CIDR <= 128) {
                return true;
            }
        }
        
        // Validate if an integer
        function isValidInt($int) {
            if(filter_var($int, FILTER_VALIDATE_INT)) {
                return true;
            }
        }
               
        // Validate port or port range
        function getValidPortRange($ports) {
            $ports = str_replace(' ', '', $ports);
            if(!preg_match('/^[0-9]{1,5}(-[0-9]{1,5}){0,1}$/', $ports)) {
                return false;
            }
            $portsExploded = explode("-", $ports);
            // If $portsExploded contains only 1 field, duplicate it to index[1]
            if(sizeof($portsExploded) == 1) {
                $portsExploded[1] = $portsExploded[0];
            }
            if(!($portsExploded[0] >= 1 && $portsExploded[0] <= 65535 && $portsExploded[1] >= 1 && $portsExploded[1] <= 65535)) {
                return false;
            }
            // Check that the second port is higher than the first.
            if($portsExploded[1] < $portsExploded[0]) {
                return false;
            }
            return $portsExploded;
        }
        
        // Get a wildcard mask from a CIDR mask
        function getWildcardMask($cidr) {
            $subnetMask = $this->convertCIDRToSubnetMask($cidr);
            $decimal = ip2long($subnetMask);
            $binary = decbin($decimal);
            $binaryInverse = strtr($binary,[1,0]);
            $decimalInverse = bindec($binaryInverse);
            $subnetMaskInverse = long2ip($decimalInverse);
            return $subnetMaskInverse;
        }
        
    }

?>