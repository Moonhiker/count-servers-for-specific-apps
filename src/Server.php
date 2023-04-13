<?php
declare(strict_types=1);
namespace CheckServer;

require_once "Information.php";

class Server{

    private array $ServerInfoArray = [];
    private Information $ServerInformation;
    private array $AppsInfoArray = [];


    public function __construct(array $ServerinfoArray, array $App_info)
    {   
        $this->ServerInformation = new Information($ServerinfoArray);

        if($this->validParameter($this->ServerInformation))
        {
        $this->createNewServer();
        $this->createApplications($App_info);
        } 
    }

    public function validParameter(Information $Parameter)
    {
        if( $Parameter->getCPU() > 0.0 && $Parameter->getBrandwidth() > 0.0 &&
        $Parameter->getRam() > 0.0 )
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }

    public function createApplications(array $App_info) : void
    {
        $AppsNotFitIntoServerCount = 0;
        foreach($App_info as $info)
        {
            $Application_Info = new Information($info);
            if(!$this->validParameter($Application_Info)){ continue; }
            
            if(!$this->fitsAppIntoServer($this->ServerInformation, $Application_Info)) // check if parameter from App are to big for server
            {
                $Application_Info->setServerName("Not fit to any server");
                $AppsNotFitIntoServerCount++;
            }

            array_push($this->AppsInfoArray, $Application_Info);
        }

        if(count($this->AppsInfoArray) == 0 || $AppsNotFitIntoServerCount == count($this->AppsInfoArray)) // no App fits or no App were given
        {
            $this->ServerInfoArray = [];
        }
    }

    public function createNewServer() : void
    {
        array_push($this->ServerInfoArray, clone($this->ServerInformation));
    }

    public function fitsAppIntoServer(Information $server, Information $application) : bool
    {
        if( $server->getCPU() >= $application->getCPU() && 
            $server->getBrandwidth() >= $application->getBrandwidth() &&
            $server->getRam() >= $application->getRam()) 
        {
            return true;
        }
        else{
            return false;
        }
                    
    }

    public function subractAppInfoFromServer(Information &$server, Information $application) : void
    {
                    $server->setCPU($server->getCPU() - $application->getCPU());  
                    $server->setBrandwidth($server->getBrandwidth() - $application->getBrandwidth());
                    $server->setRam($server->getRam() - $application->getRam()); 
    }

    public function getUsedServerCount() : int
    {
       
        return count($this->ServerInfoArray);
    }

    public function getUsedServers() : array
    {
        return $this->ServerInfoArray;
    }

    public function getFilledApplications() : array
    {
        return $this->AppsInfoArray;
    }

    public function clearEmptyServer() : void
    {
        for ( $i=0; $i< count($this->ServerInfoArray); $i++ )
        {
            if($this->ServerInfoArray[$i]->getAppsOnServer()=="")
            {
                unset($this->ServerInfoArray[$i]);
            }
        }
    } 

    public function firstFitAlgorithm() : void 
    {
        for($app_index=0; $app_index < count($this->AppsInfoArray); $app_index++  )
        {
            for($ser_index=0; $ser_index < count($this->ServerInfoArray); $ser_index++  )
            {
                if(!empty($this->AppsInfoArray[$app_index]->getServerName()))  break;

                if($this->fitsAppIntoServer($this->ServerInfoArray[$ser_index], $this->AppsInfoArray[$app_index]))
                {  
                    $this->subractAppInfoFromServer($this->ServerInfoArray[$ser_index], $this->AppsInfoArray[$app_index]);

                    $this->AppsInfoArray[$app_index]->setServerName("Server ". $ser_index + 1);
                    $this->ServerInfoArray[$ser_index]->setAppsOnServer($this->AppsInfoArray[$app_index]->getName());
                    break;
                }
                else
                {
                     $this->createNewServer();
                }
            }
        }

        $this->clearEmptyServer();
    }

}

// JUST FOR TEST
// $ServerinfoArray = ["CPU_in_GHz" => 8, "Band_in_Gbit" => 200, "Ram_in_GB" => 8, "Name" => "DELL"];


// $App_info = [
//     ["CPU_in_GHz" => 3, "Band_in_Gbit" => 30, "Ram_in_GB" => 1, "Name" => "App1"],
//     ["CPU_in_GHz" => 6, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App2"],
//     ["CPU_in_GHz" => 4, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App3"],
//     ["CPU_in_GHz" => 2, "Band_in_Gbit" => 50, "Ram_in_GB" => 2.5, "Name" => "App4"],
//     ["CPU_in_GHz" => 2, "Band_in_Gbit" => 70, "Ram_in_GB" => 1.5, "Name" => "App5"]];
    
// $Server = new Server($ServerinfoArray, $App_info);
// $Server->firstFitAlgorithm();

// $Apps = $Server->getFilledApplications();
// foreach($Apps as $App){
//    echo $App->getName() . " works on " . $App->getServerName() . "<br>";
// }

// echo "We need " . $Server->getUsedServerCount() . " Server(s) to run " . count($Apps) . " App(s) <br>";

// $DellServer = $Server->getUsedServers();
// echo "Apps on Server 1= " . $DellServer[0]->getAppsOnServer();

?>