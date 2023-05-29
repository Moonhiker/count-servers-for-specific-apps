<?php
declare(strict_types=1);
namespace CheckServer;

class Information{

private float $CPU;
private float $Brandwidth;
private float $Ram;
private string $Name;
private string $ServerName;
private string $AppsOnServer;


public function __construct(array $info)
{
    // unfortunately not possible when key not exist in array and we 
    //$this->CPU = (float) $info["CPU_in_GHz"] ?? 0.0; 
    //$this->Brandwidth = (float) $info["Band_in_Gbit"] ?? 0.0;
    //$this->Ram = (float) $info["Ram_in_GB"] ?? 0.0;
    //$this->Name = (string) $info["Name"] ?? "";


    $this->CPU = isset( $info["CPU_in_GHz"]) ? (float)$info["CPU_in_GHz"] : 0.0;
    $this->Brandwidth = isset( $info["Band_in_Gbit"]) ? (float)$info["Band_in_Gbit"] : 0.0;
    $this->Ram = isset( $info["Ram_in_GB"]) ? (float)$info["Ram_in_GB"] : 0.0;
    $this->Name = isset( $info["Name"]) ? (string)$info["Name"] : "";
    $this->ServerName = "";
    $this->AppsOnServer = "";
}


public function getCPU() : float
{
    return $this->CPU;
}

public function getBrandwidth() : float
{
    return $this->Brandwidth;
}

public function getRam() : float
{
    return $this->Ram;
}

public function getName() : string
{
    return $this->Name;
}

public function getServerName() : string
{
    return $this->ServerName;
}

public function setServerName(string $name) : void
{
    $this->ServerName = $name;
}

public function setCPU(float $CPU) : void
{
    $this->CPU = $CPU ;
}

public function setBrandwidth(float $Band) : void
{
    $this->Brandwidth = $Band;
}

public function setRam(float $Ram) : void
{
    $this->Ram = $Ram;
}

public function setAppsOnServer(string $App) : void
{
    if(!empty($this->AppsOnServer))
    {
        $this->AppsOnServer .= ", ";
    }
    $this->AppsOnServer .= $App;
}

public function getAppsOnServer() : string
{
    return $this->AppsOnServer;
}
}

?>