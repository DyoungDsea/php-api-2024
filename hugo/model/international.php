<?php

class InternationalDataFetcher
{
    private $pdo;
  
    public function __construct() {
        try {
            // Create a PDO connection using your function
            $this->pdo = createPDOConnection();
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function fetchInternationalData($userid)
    {
        $routes = $this->fetchRoutes();
        $services = $this->fetchServices();
        $addresses = $this->fetchAddresses();
        $userAddresses = $this->fetchUserAddresses($userid);

        $addressData = array_merge($addresses, $userAddresses);

        return [
            "routes" => $routes,
            "service" => $services,
            "address" => $addressData
        ];
    }

    //?public function to fetch route address
    public function fetchRoute($routeId){
        $route = $this->fetchRouteAddresses($routeId);
        if($route==[]){
            $res[]=['raddress'=>"Sorry, we don't have any address yet"];
            return $res;
        }
        return $route;
    }
 

    private function fetchRoutes()
    {
        $routeQuery = $this->pdo->prepare("SELECT rid, droute FROM `droutes` WHERE dtype='International' ORDER BY droute");
        $routeQuery->execute();
        return $routeQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchServices()
    {
        $secQuery = $this->pdo->prepare("SELECT sid, dservice FROM `dservices` WHERE dtype='International' ORDER BY dservice");
        $secQuery->execute();
        $services = [];
        if ($secQuery->rowCount() > 0) {
            while ($sect = $secQuery->fetch(PDO::FETCH_ASSOC)) {
                $services[] = [
                    'sid' => $sect['sid'],
                    'service' => html_entity_decode($sect['dservice']),
                ];
            }
        }
        return $services;
    }
    

    private function fetchAddresses()
    {
        $addressQuery = $this->pdo->prepare("SELECT aid, dtitle FROM `daddress` ORDER BY dtitle");
        $addressQuery->execute();
        return $addressQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchUserAddresses($userid)
    {
        $myAddressQuery = $this->pdo->prepare("SELECT aid, dlocation FROM `myaddress` WHERE userid=:userid ORDER BY dname");
        $myAddressQuery->bindParam(':userid', $userid, PDO::PARAM_STR);
        $myAddressQuery->execute();
        $myAddresses = [];
        if ($myAddressQuery->rowCount() > 0) {
            while ($row = $myAddressQuery->fetch(PDO::FETCH_ASSOC)) {
                $myAddresses[] = [
                    'aid' => $row['aid'],
                    'dtitle' => $row['dlocation'],
                ];
            }
        }
        return $myAddresses;
    }


    //? fetch route address 
    private function fetchRouteAddresses($routeId)
    {
        $addressQuery = $this->pdo->prepare("SELECT rd.raddress FROM `droutes` dr JOIN routeaddress rd ON dr.droute=rd.droute WHERE dr.rid='$routeId' ORDER BY rd.raddress");
        $addressQuery->execute();
        return $addressQuery->fetchAll(PDO::FETCH_ASSOC);
    }
}

